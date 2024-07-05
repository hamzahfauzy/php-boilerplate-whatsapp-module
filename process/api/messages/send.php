<?php

use Core\Database;
use Core\Request;
use Core\Response;

$responseCode = 405;
$message = "Method is not allowed";
if(Request::isMethod('POST'))
{
    if(isset($_POST['phone']))
    {
        $db = new Database;
        $contact = $db->single('wa_contacts', ['phone' => $_POST['phone'], 'user_id' => auth()->id]);
        if(empty($contact))
        {
            $contact = $db->insert('wa_contacts', [
                'name' => $_POST['phone'],
                'phone' => $_POST['phone'],
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