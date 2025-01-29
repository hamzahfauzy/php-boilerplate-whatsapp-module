<?php

use Core\Database;
use Core\Log;

$db = new Database;

// get expired reply session
$expDate = date('Y-m-d H:i:s');
Log::write("Expired notification start at : $expDate");
$db->query = "SELECT wa_reply_sessions.*, wa_devices.user_id device_user_id, wa_campaign_items.item_status campaign_status FROM wa_reply_sessions JOIN wa_devices ON wa_devices.id = wa_reply_sessions.device_id LEFT JOIN wa_campaign_items ON wa_campaign_items.session_id = wa_reply_sessions.id WHERE wa_reply_sessions.`status` = 'ACTIVE' AND wa_reply_sessions.expired_at < NOW()";
$expiredSessions = $db->exec('all');

Log::write("Execute : ". json_encode($expiredSessions));

foreach($expiredSessions as $session)
{
    if($session->campaign_status && $session->campaign_status != 'EXPIRED')
    {
        $db->query = "UPDATE wa_campaign_items SET `item_status` = 'EXPIRED', updated_at = NOW() WHERE session_id = $session->id";
        $db->exec();
    }
    else
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
    }

    $db->query = "UPDATE wa_reply_sessions SET `status` = 'EXPIRED', updated_at = NOW() WHERE id = $session->id";
    $db->exec();
}