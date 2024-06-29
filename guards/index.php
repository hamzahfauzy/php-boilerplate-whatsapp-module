<?php

use Core\Database;

$db = new Database;
$auth = auth();

if($route == 'whatsapp/webhook')
{
    return true;
}

if(empty($auth))
{
    header('location:'.routeTo('auth/login'));
    die;
}

if(in_array($route, ['whatsapp/devices/detail', 'whatsapp/devices/logout']))
{
    $device = $db->single('wa_devices', [
        'id' => $_GET['id']
    ]);
    
    $isAllowed = ($device->user_id == auth()->id || get_role($auth->id)->role_id == 1);
    if(!$isAllowed)
    {
        set_flash_msg(['error'=>'Unauthorized']);
        header("location: ".$_SERVER['HTTP_REFERER']);
        die();
    }
}

return true;