<?php

use Core\Page;
use Core\Database;

$id = $_GET['id'];
$db = new Database;

$db->update('wa_devices', [
    'status' => 'LOGOUT'
], [
    'id' => $id
]);

set_flash_msg(['success'=>'Logout Success']);
header("location: ".$_SERVER['HTTP_REFERER']);
die();