<?php

use Core\Database;
use Core\JwtAuth;
use Core\Response;

if(!JwtAuth::validateBearerToken() || empty(jwtAuth()))
{
    echo Response::json([], 'Unauthorized', 401);
    die();
}

if($route == 'api/whatsapp/messages/send')
{
    $db = new Database();
    $userId = auth()->id;
    $isSuperAdmin = get_role($userId)->role_id == 1;
    $isOwner = $db->exists('wa_devices', [
        'id' => $_POST['device_id'],
        'user_id' => $userId
    ]);

    if(!$isOwner && !$isSuperAdmin)
    {
        echo Response::json([], 'Unauthorized', 401);
        die();
    }
}