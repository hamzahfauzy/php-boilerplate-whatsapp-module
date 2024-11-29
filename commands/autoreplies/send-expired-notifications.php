<?php

use Core\Database;
use Core\Log;

$db = new Database;

// get expired reply session
$expDate = date('Y-m-d H:i:s');
Log::write("Expired notification start at : $expDate");
$db->query = "SELECT wa_reply_sessions.*, wa_devices.user_id device_user_id FROM wa_reply_sessions JOIN wa_devices ON wa_devices.id = wa_reply_sessions.device_id WHERE wa_reply_sessions.`status` = 'ACTIVE' AND expired_at < NOW()";
$expiredSessions = $db->exec('all');

foreach($expiredSessions as $session)
{
    // get setting
    $setting = $db->single('wa_reply_settings',[
        'user_id' => $session->device_user_id
    ]);

    $expiration_message = $setting->expiration_message;

    // send expiration message to user
    $data = [
        'device_id'  => $session->device_id,
        'contact_id' => [$session->contact_id],
        'content'    => $expiration_message
    ];

    Log::write("Send Expired notification to : ". json_encode($data));
    $sendMessage = whatsappSendMessage($data, $session->device_user_id);

    $db->query = "UPDATE wa_reply_sessions SET `status` = 'EXPIRED', updated_at = NOW() WHERE id = $session->id";
    $db->exec();
}