<?php

use Core\Database;

$user_id = auth()->id;
$db = new Database;

$db->query = "DELETE FROM wa_reply_sessions WHERE device_id IN (SELECT device_id FROM wa_devices WHERE user_id = $user_id)";
$db->exec();

set_flash_msg(['success'=>"Sesi berhasil dihapus"]);

header('location:'.routeTo('whatsapp/autoreplies/setting'));
die();