import {makeWASocket, DisconnectReason, useMultiFileAuthState } from '@whiskeysockets/baileys'
import { Boom } from '@hapi/boom';
import { unlinkSync } from 'fs';
import { join } from 'path';

const startWhatsApp = async () => {
    // Path untuk menyimpan file sesi
    const authFile = 'auth_info';
    const { state, saveCreds } = await useMultiFileAuthState(authFile);

    const sock = makeWASocket({
        auth: state,
        printQRInTerminal: true // Menampilkan QR code di terminal
    });

    // Menyimpan sesi secara otomatis
    sock.ev.on ('creds.update', saveCreds)

    // Event saat koneksi putus
    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect } = update;
        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect.error = Boom.isBoom && lastDisconnect.error.output.statusCode !== DisconnectReason.loggedOut);
            console.log('Connection closed due to ', lastDisconnect.error, ', reconnecting ', shouldReconnect);
            if (shouldReconnect) {
                startWhatsApp();
            } else {
                unlinkSync(authFile); // Hapus file sesi jika logout
            }
        } else if (connection === 'open') {
            console.log('Connected');
        }
    });

    // Event saat menerima pesan
    sock.ev.on('messages.upsert', (m) => {
        console.log(JSON.stringify(m, null, 2));

        const message = m.messages[0];
        if (!message.key.fromMe && m.type === 'notify') {
            sock.sendMessage(message.key.remoteJid, { text: 'Hello, this is an automated reply!' });
        }
    });
}

startWhatsApp();
