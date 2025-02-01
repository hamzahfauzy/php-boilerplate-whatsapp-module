<?php

use Core\Route;

Route::additional_allowed_routes([
    'route_path' => '!crud/edit?table=wa_campaigns',
]);

$role = get_role(auth()->id);

if($role->role_id != 1)
{
    $_GET['filter']['user_id'] = auth()->id;
}