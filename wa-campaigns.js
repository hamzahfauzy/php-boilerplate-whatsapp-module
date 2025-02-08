import * as fs from 'fs'
import dotenv from 'dotenv'
import mysql from 'mysql2/promise';
import axios from 'axios'

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

async function sleep(millis) {
    return new Promise(resolve => setTimeout(resolve, millis));
}

function getRandomArbitrary(min, max) {
    return Math.random() * (max - min) + min;
}

async function sendMessage(msg)
{
    console.log(msg)
    await axios.post('http://127.0.0.1:3000/send-message', msg).catch(error => {
        console.log(error)
    });
}

// A simple SELECT query
while(true)
{
    try {
        // direct message
        const [messages] = await db.query(
            'SELECT `wa_messages`.*, `wa_contacts`.`phone` FROM `wa_messages` JOIN `wa_contacts` ON `wa_contacts`.`id` = `wa_messages`.`contact_id` LEFT JOIN wa_campaign_items ON wa_campaign_items.message_id = wa_messages.id LEFT JOIN wa_devices ON wa_devices.id = wa_messages.device_id WHERE `wa_messages`.`status` = ? AND `wa_messages`.`scheduled_at` IS NULL AND wa_campaign_items.message_id IS NOT NULL AND wa_devices.status = "CONNECTED" LIMIT 20',
            ["WAITING"]
        );

        if(messages.length)
        {
            for(const message in messages)
            {
                const msg = messages[message]
                await sendMessage(msg)
                
                const timer = getRandomArbitrary(5, 20)
                await sleep(timer * 1000)
            }
        }

        // scheduled message
        const [schedules] = await db.query(
            'SELECT `wa_messages`.*, `wa_contacts`.`phone` FROM `wa_messages` JOIN `wa_contacts` ON `wa_contacts`.`id` = `wa_messages`.`contact_id` LEFT JOIN wa_campaign_items ON wa_campaign_items.message_id = wa_messages.id LEFT JOIN wa_devices ON wa_devices.id = wa_messages.device_id WHERE `wa_messages`.`status` = ? AND wa_campaign_items.message_id IS NOT NULL AND wa_devices.status = "CONNECTED" AND DATE_FORMAT(`wa_messages`.`scheduled_at`, "%Y-%m-%d %H:%i") <= DATE_FORMAT(now(), "%Y-%m-%d %H:%i")',
            ["WAITING"]
        );

        if(schedules.length)
        {
            for(const message in schedules)
            {
                const msg = schedules[message]
                await sendMessage(msg)
            }
        }

    } catch (err) {
        console.log(err);
    }

    await sleep(5000);
}