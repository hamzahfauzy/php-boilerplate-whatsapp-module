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
        auth: state
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

        if(newDevices.length)
        {
            axios.post(newDevices[0].webhook_url, JSON.stringify({
                device:device, 
                from:from, 
                content:content
            })).catch(error => {
                console.log(error)
            });
        }
        // console.log('replying to', m.messages[0].key.remoteJid)
        // await sock.sendMessage(m.messages[0].key.remoteJid, { text: 'Hello there!' })
    })
}

function doLogout(device)
{
    // delete session file
    if (fs.existsSync(`wa_session/device-${device.id}`)) {
        fs.rmSync(`wa_session/device-${device.id}`, { recursive: true, force: true });

        db.query('UPDATE `wa_devices` SET `status` = ?, `phone` = NULL WHERE `id` = ?', ["NOT CONNECTED", device.id])
        
        delete devices[device.id]
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
                if(!Array.isArray(devices[msg.device_id]) && devices[msg.device_id] != undefined)
                {
                    const response = devices[msg.device_id].sendMessage(msg.phone + '@s.whatsapp.net', { text: msg.content })
                    db.query(
                        'UPDATE `wa_messages` SET `status` = ?, `response` = ? WHERE `id` = ? ',
                        ["SENT", JSON.stringify(response), msg.id]
                    );
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
                if(!Array.isArray(devices[msg.device_id]) && devices[msg.device_id] != undefined)
                {
                    const response = devices[msg.device_id].sendMessage(msg.phone + '@s.whatsapp.net', { text: msg.content })
                    db.query(
                        'UPDATE `wa_messages` SET `status` = ?, `response` = ? WHERE `id` = ?',
                        ["SENT", JSON.stringify(response), msg.id]
                    );
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
}