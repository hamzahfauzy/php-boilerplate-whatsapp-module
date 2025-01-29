import {makeWASocket, DisconnectReason, useMultiFileAuthState } from '@whiskeysockets/baileys'
import * as fs from 'fs'
import dotenv from 'dotenv'
import mysql from 'mysql2/promise';
import axios from 'axios'
import { toDataURL } from "qrcode"

var devices = []

dotenv.config({path: '../../.env'})

const db = await mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
    port: process.env.DB_PORT,
    multipleStatements: true,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
});

async function connectToWhatsApp (device) {

    const { state, saveCreds } = await useMultiFileAuthState('wa_session/device-'+device.id)

    const sock = makeWASocket({
        // can provide additional config here
        printQRInTerminal: false,
        auth: state,
        // defaultQueryTimeoutMs: undefined
    })
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update
        if(connection === 'close') {
            const shouldReconnect = (lastDisconnect.error)?.output?.statusCode !== DisconnectReason.loggedOut
            // console.log('connection closed due to ', lastDisconnect.error, ', reconnecting ', shouldReconnect)

            // reconnect if not logged out
            if(shouldReconnect) {
                connectToWhatsApp(device)
            }
            else
            {
                doLogout(device)
            }
        }
        
        if(connection === 'open') {
            devices[device.id] = sock
            var credsId = state.creds.me.id
            const phone = credsId.split('@')[0].split(':')[0]
            db.query(
                'UPDATE `wa_devices` SET `status` = ?, `qrcode` = NULL, `phone` = ?  WHERE `id` = ?',
                ["CONNECTED", phone, device.id]
            )
        }

        if (qr) 
        {
            // update qr code to devices
            const qrcode = await toDataURL(qr)
            db.query(
                'UPDATE `wa_devices` SET `qrcode` = ? WHERE `id` = ?',
                [qrcode, device.id]
            )
        }
    })
    sock.ev.on ('creds.update', saveCreds)
    sock.ev.on('messages.upsert', async m => {
        // console.log(JSON.stringify(m, undefined, 2))
        if(m.hasOwnProperty('type') && m.type == 'append') return;
        if(!m.messages[0].hasOwnProperty('message')) return;
        if(!(m.messages[0].message.hasOwnProperty('extendedTextMessage') || m.messages[0].message.hasOwnProperty('conversation'))) return
        var from = m.messages[0].key.remoteJid.split('@')[0].split(':')[0]
        var [results] = await db.query(
            'SELECT * FROM `wa_contacts` WHERE `phone` = ? AND `user_id` = ?',
            [from, device.user_id]
        );

        if(!results.length)
        {
            await db.query(
                'INSERT INTO `wa_contacts` (user_id,name,phone,created_by) VALUES(?,?,?,?)',
                [device.user_id, m.messages[0].pushName, from, device.user_id]
            );
        }

        const recordType = m.messages[0].key.fromMe ? 'MESSAGE_OUT' : 'MESSAGE_IN';
        const content = m.messages[0].message.hasOwnProperty('extendedTextMessage') ? m.messages[0].message.extendedTextMessage.text : m.messages[0].message.conversation
        const response = JSON.stringify(m)
        db.query(
            'INSERT INTO `wa_messages` (device_id,contact_id,content,status,record_type,created_by,response) VALUES (?,(SELECT id FROM `wa_contacts` WHERE `phone` = ? AND `user_id` = ?), ?, ?, ?, ?, ?)', 
            [device.id, from, device.user_id, content, "SENT", recordType, device.user_id, response]
        )

        // webhook
        var [newDevices] = await db.query(
            'SELECT * FROM `wa_devices` WHERE `webhook_url` IS NOT NULL AND `id` = ?',
            [device.id]
        );

        if(newDevices.length && newDevices[0].webhook_url != '' && recordType == 'MESSAGE_IN')
        {
            axios.post(newDevices[0].webhook_url, JSON.stringify({
                device:device, 
                from:from, 
                content:content
            })).catch(error => {
                console.log(error)
            });
        }

        // autoreply
        if(recordType == 'MESSAGE_IN')
        {
            autoreply(results[0], content, device)
        }
        // console.log('replying to', m.messages[0].key.remoteJid)
        // await sock.sendMessage(m.messages[0].key.remoteJid, { text: 'Hello there!' })
    })
}

async function doLogout(device)
{
    // delete session file
    try {
        await devices[device.id].logout()
    } catch {
    } finally {
        db.query('UPDATE `wa_devices` SET `status` = ?, `phone` = NULL WHERE `id` = ?', ["NOT CONNECTED", device.id])
        if (fs.existsSync(`wa_session/device-${device.id}`)) {
            fs.rmSync(`wa_session/device-${device.id}`, { recursive: true, force: true });
        }
        delete devices[device.id]
    }
}

async function autoreply(contact, content, device)
{
    // check user reply setting is active
    var [replySetting] = await db.query(
        'SELECT * FROM `wa_reply_settings` WHERE `reply_status` = "ACTIVE" AND `user_id` = ?',
        [device.user_id]
    );

    if(replySetting.length)
    {
        // find reply session
        var [replySession] = await db.query(
            'SELECT * FROM wa_reply_sessions WHERE `device_id` = ? AND `contact_id` = ? AND `status` = "ACTIVE"',
            [device.id, contact.id]
        )

        // if reply session is not exists
        if(!replySession.length)
        {
            // create reply session
            await db.query(
                'INSERT INTO wa_reply_sessions(device_id,contact_id) VALUES (?,?)',
                [device.id,contact.id]
            )
        }

        var [replySession] = await db.query(
            'SELECT wa_reply_sessions.*, wa_campaign_items.item_status campaign_status FROM wa_reply_sessions LEFT JOIN wa_campaign_items ON wa_campaign_items.session_id = wa_reply_sessions.id WHERE `device_id` = ? AND `contact_id` = ? AND `status` = "ACTIVE"',
            [device.id, contact.id]
        )

        if(replySession[0].campaign_status && replySession[0].campaign_status == 'WAITING')
        {
            // need campaign reply
            await db.query(
                'UPDATE wa_reply_sessions SET `status` = ? WHERE `id` = ?',
                ['EXPIRED', replySession[0].id]
            )

            await db.query(
                'UPDATE wa_campaign_items SET `item_status` = ? , `response` = ? WHERE `session_id` = ?',
                ['REPLIED', content, replySession[0].id]
            )
            return
        }

        const replyContent = (replySession[0].session_data ? replySession[0].session_data + "\r\n" : '') + content

        // find reply
        var [reply] = await db.query(
            "SELECT * FROM `wa_replies` WHERE `device_id` = ? AND ? REGEXP CONCAT(REPLACE(trim(keyword), '*', '.*'),'$')",
            [device.id, replyContent]
        )

        if(reply.length)
        {
            if(reply[0].content != '')
            {
                var replyText = reply[0].content
                if(reply[0].reply_type == 'WEBHOOK')
                {
                    try {
                        const payload = {
                            device:device.phone, 
                            from:contact.phone, 
                            message:content,
                            content:replyContent,
                        };
                        
                        const response = await axios.post(replyText, payload).catch(error => {
                            console.log(error)
                        });
    
                        replyText = response.data.data
                    } catch (error) {
                        console.log(error)
                    }
                    
                }

                devices[device.id].sendMessage(contact.phone + '@s.whatsapp.net', {text: replyText})
            }

            var sessionData = replySession[0].session_data ?? ''
            var status = replySession[0].status

            if(reply[0].action_after == 'NEXT')
            {
                sessionData = (sessionData ? sessionData + "\r\n" : '') + content
            }
            else if(reply[0].action_after == 'BACK')
            {
                sessionData = sessionData.substring(0, sessionData.lastIndexOf("\r\n"))

                // send back reply
                var [backReply] = await db.query(
                    'SELECT * FROM `wa_replies` WHERE `device_id` = ? AND trim(`keyword`) = ?',
                    [device.id, sessionData]
                )

                if(backReply[0].content != '')
                {
                    devices[device.id].sendMessage(phone + '@s.whatsapp.net', {text: backReply[0].content})
                }
            }
            else if(reply[0].action_after == 'EXIT')
            {
                status = 'EXPIRED'
            }

            // update session data
            await db.query(
                'UPDATE wa_reply_sessions SET `session_data` = ?, `status` = ?, `expired_at` = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE `id` = ?',
                [sessionData, status, replySetting[0].expiration_time, replySession[0].id]
            )
        }
    }
}

// run in main file
// connectToWhatsApp()
try {
    const [results] = await db.query(
      'SELECT * FROM `wa_devices` WHERE `status` = ?', ["CONNECTED"]
    );

    if(results.length)
    {
        for(const result in results)
        {
            const row = results[result]
            if(devices[row.id] == undefined)
            {
                devices[row.id] = []
                connectToWhatsApp(row)
            }
        }
    }
} catch (err) {
    console.log(err);
}

async function sleep(millis) {
    return new Promise(resolve => setTimeout(resolve, millis));
}

// A simple SELECT query
while(true)
{
    try {
        const [results] = await db.query(
            'SELECT * FROM `wa_devices` WHERE `status` = ?', ["NOT CONNECTED"]
        );

        if(results.length)
        {
            for(const result in results)
            {
                const row = results[result]
                if(devices[row.id] == undefined)
                {
                    devices[row.id] = []
                    connectToWhatsApp(row)
                }
            }
        }


        // direct message
        const [messages] = await db.query(
            'SELECT `wa_messages`.*, `wa_contacts`.`phone` FROM `wa_messages` JOIN `wa_contacts` ON `wa_contacts`.`id` = `wa_messages`.`contact_id` WHERE `wa_messages`.`status` = ? AND `wa_messages`.`scheduled_at` IS NULL',
            ["WAITING"]
        );

        if(messages.length)
        {
            for(const message in messages)
            {
                const msg = messages[message]
                if(!Array.isArray(devices[msg.device_id]) && devices[msg.device_id] != undefined && msg.phone)
                {
                    const [result] = await devices[msg.device_id].onWhatsApp(msg.phone)
                    // console.log(result)
                    if (result && result.exists)
                    {
                        console.log("send message to " + msg.phone)
                        const response = await devices[msg.device_id].sendMessage(msg.phone + '@s.whatsapp.net', JSON.parse(msg.message_data))
                        db.query(
                            'UPDATE `wa_messages` SET `status` = ?, `response` = ? WHERE `id` = ? ',
                            ["SENT", JSON.stringify(response), msg.id]
                        );
                    }
                    else
                    {
                        db.query(
                            'UPDATE `wa_messages` SET `status` = ?, `response` = ? WHERE `id` = ? ',
                            ["ERROR", JSON.stringify({message: 'Not exists'}), msg.id]
                        );
                    }
                }
            }
        }

        // scheduled message
        const [schedules] = await db.query(
            'SELECT `wa_messages`.*, `wa_contacts`.`phone` FROM `wa_messages` JOIN `wa_contacts` ON `wa_contacts`.`id` = `wa_messages`.`contact_id` WHERE `wa_messages`.`status` = ? AND DATE_FORMAT(`wa_messages`.`scheduled_at`, "%Y-%m-%d %H:%i") = DATE_FORMAT(now(), "%Y-%m-%d %H:%i")',
            ["WAITING"]
        );

        if(schedules.length)
        {
            for(const message in schedules)
            {
                const msg = schedules[message]
                if(!Array.isArray(devices[msg.device_id]) && devices[msg.device_id] != undefined && msg.phone)
                {
                    const [result] = await devices[msg.device_id].onWhatsApp(msg.phone)
                    // console.log(result)
                    if (result && result.exists)
                    {
                        console.log("send message to " + msg.phone)
                        const response = await devices[msg.device_id].sendMessage(msg.phone + '@s.whatsapp.net', JSON.parse(msg.message_data))
                        db.query(
                            'UPDATE `wa_messages` SET `status` = ?, `response` = ? WHERE `id` = ?',
                            ["SENT", JSON.stringify(response), msg.id]
                        );
                    }
                    else
                    {
                        db.query(
                            'UPDATE `wa_messages` SET `status` = ?, `response` = ? WHERE `id` = ? ',
                            ["ERROR", JSON.stringify({message: 'Not exists'}), msg.id]
                        );
                    }
                }
            }
        }
        
        const [logouts] = await db.query(
            'SELECT * FROM `wa_devices` WHERE `status` = ?',
            ["LOGOUT"]
        );

        if(logouts.length)
        {
            for(const logout in logouts)
            {
                const data = logouts[logout]
                doLogout(data)
            }
        }

    
    } catch (err) {
        console.log(err);
    }

    await sleep(5000);
}