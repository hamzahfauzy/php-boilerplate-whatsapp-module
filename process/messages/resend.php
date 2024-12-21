<?php

use Core\Page;
use Core\Database;

$id = $_GET['id'];
$db = new Database;

$db->update('wa_messages', [
    'status' => 'WAITING'
], [
    'id' => $id
]);

set_flash_msg(['success'=>'Resend Success']);
header("location: ".$_SERVER['HTTP_REFERER']);
die();