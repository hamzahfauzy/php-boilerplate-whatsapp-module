<?php
$contacts = $_POST['contacts'];

$message_data = [
    'device_id' => $data->device_id,
    'content' => $data->content,
    'contact_id' => $contacts
];

$expired_at = $data->expiring_time;

whatsappSendMessage($message_data, auth()->id, function($message) use ($data, $db, $expired_at){
    $db->table = "wa_reply_sessions";
    $db->query = "INSERT INTO wa_reply_sessions (device_id,contact_id,session_data,expired_at)VALUES($data->device_id,$message->contact_id,'$message->content',DATE_ADD(NOW(), INTERVAL $expired_at MINUTE))";
    $session = $db->exec('insert');
    $db->query = "INSERT INTO wa_campaign_items (campaign_id,message_id,session_id,expired_at,created_by)VALUES($data->id,$message->id,$session->id,DATE_ADD(NOW(), INTERVAL $expired_at MINUTE),$message->created_by)";
    $db->exec();

});