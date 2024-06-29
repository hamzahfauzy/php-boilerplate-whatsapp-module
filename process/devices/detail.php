<?php

use Core\Page;
use Core\Database;

$id = $_GET['id'];
$db = new Database;
$error_msg  = get_flash_msg('error');
$success_msg  = get_flash_msg('success');

$device = $db->single('wa_devices',[
    'id' => $id
]);

// page section
$title = 'Device - '.$device->name;
Page::setActive("whatsapp.wa_devices");
Page::setTitle($title);
Page::setModuleName($title);
Page::setBreadcrumbs([
    [
        'url' => routeTo('/'),
        'title' => __('crud.label.home')
    ],
    [
        'url' => routeTo('crud/index', ['table' => 'devices']),
        'title' => 'Devices'
    ],
    [
        'title' => 'Detail'
    ]
]);

return view('whatsapp/views/devices/detail', compact('device','error_msg','success_msg'));