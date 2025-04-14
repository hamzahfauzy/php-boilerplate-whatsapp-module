<?php

use Core\Database;
use Core\Request;
use Core\Response;

$responseCode = 405;
$message = "Method is not allowed";
if(Request::isMethod('POST'))
{
    // device validation
    if(!isset($_POST['device_id']))
    {
        return Response::json([], 'Device id is required', 400);
    }

    $db = new Database;
    $device = $db->single('wa_devices', [
        'id' => $_POST['device_id']
    ]);
    if(empty($device))
    {
        return Response::json([], 'Device is not exists', 400);
    }

    if($_POST['type'] == 'location' && (!isset($_POST['location']) || empty($_POST['location']) || !isset($_POST['location']['lat']) || !isset($_POST['location']['lng'])))
    {
        return Response::json([], 'Location is not valid', 400);
    }
    
    if($_POST['type'] == 'polling' && (!isset($_POST['polling']) || empty($_POST['polling']) || !isset($_POST['polling']['name']) || !isset($_POST['polling']['value'])))
    {
        return Response::json([], 'Polling is not valid', 400);
    }
    
    if($_POST['type'] == 'media' && (!isset($_POST['media']) || empty($_POST['media']) || !isset($_POST['media']['name']) || !isset($_POST['media']['url'])))
    {
        return Response::json([], 'Media is not valid', 400);
    }

    if(isset($_POST['phone']))
    {   
        $contact_number = strpos($_POST['phone'], '@') > -1 ? explode('@', $_POST['phone']) : $_POST['phone'];
        $contact_number = is_array($contact_number) ? $contact_number[0] : $contact_number;
        $contact = $db->single('wa_contacts', ['phone' => $contact_number, 'user_id' => auth()->id]);
        if(empty($contact))
        {
            $contact = $db->insert('wa_contacts', [
                'name' => $_POST['phone'],
                'phone' => $contact_number,
                'remoteJid' => strpos($_POST['phone'], '@') ? $_POST['phone'] : $contact_number.'@s.whatsapp.net',
                'user_id' => auth()->id,
                'created_by' => auth()->id
            ]);
        }

        $_POST['contact_id'][] = $contact->id;
        unset($_POST['phone']);
    }

    $sendMessage = whatsappSendMessage($_POST, auth()->id);
    $message = $sendMessage['message'];
    $responseCode = $sendMessage['success'] ? 200 : 400;
}

return Response::json([], $sendMessage['message'], $responseCode);