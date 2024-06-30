<?php

use Core\Request;
use Core\Response;

$responseCode = 405;
$message = "Method is not allowed";
if(Request::isMethod('POST'))
{
    $sendMessage = whatsappSendMessage($_POST, auth()->id);
    $message = $sendMessage['message'];
    $responseCode = $sendMessage['success'] ? 200 : 400;
}

return Response::json([], $sendMessage['message'], $responseCode);