import {
  makeWASocket,
  useMultiFileAuthState,
  fetchLatestBaileysVersion,
} from "@whiskeysockets/baileys";
import pino from "pino";

async function init() {
  const { state, saveCreds } = await useMultiFileAuthState("session");

  // Fetch the latest version of WA Web and Baileys
  const { version, isLatest } = await fetchLatestBaileysVersion();
  console.log(`using WA v${version.join(".")}, isLatest: ${isLatest}`);

  const socket = makeWASocket({
    version, // Use the fetched version
    auth: state,
    logger: pino({ level: "silent" }), // Use silent logger for cleaner output
  });

  socket.ev.on("creds.update", saveCreds);

  // You should listen for the 'connection.update' event to handle the QR code
  socket.ev.on("connection.update", (update) => {
    const { connection, qr } = update;
    if (qr) {
      console.log("Scan this QR code with your phone: ", qr);
      // The QR will be printed in the terminal by `printQRInTerminal`
    }
    if (connection === "close") {
      console.log("Connection closed, you will need to restart the process.");
    } else if (connection === "open") {
      console.log("Connected!");
    }
  });

  return socket;
}

init();
