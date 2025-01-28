<?php

$data['created_by'] = auth()->id;
$data['user_id'] = auth()->id;

$_POST['contacts'] = $data['contacts'];
unset($data['contacts']);

if(empty($data['scheduled_at']))
{
    unset($data['scheduled_at']);
}