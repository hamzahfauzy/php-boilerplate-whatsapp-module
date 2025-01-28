<?php
$contacts = $_POST['contacts'];

$message_data = [
    'device_id' => $data->device_id,
    'content' => $data->content,
    'contact_id' => $contacts
];

$expired_at = date('Y-m-d H:i:s', strtotime('now + '.$data->expiring_time.' minutes'));

whatsappSendMessage($message_data, auth()->id, function($message) use ($data, $db, $expired_at){
    $session = $db->insert('wa_reply_sessions', [
        'device_id' => $data->device_id,
        'contact_id' => $message->contact_id,
        'session_data' => $message->content,
        'expired_at' => $expired_at
    ]);

    $db->insert('wa_campaign_items', [
        'campaign_id' => $data->id,
        'message_id' => $message->id,
        'session_id' => $session->id,
        'expired_at' => $expired_at,
        'created_by' => $message->created_by,
    ]);
});