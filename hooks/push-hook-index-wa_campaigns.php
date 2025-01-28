<?php

use Core\Route;

Route::additional_allowed_routes([
    'route_path' => '!crud/create?table=wa_campaign_items',
]);
// Route::additional_allowed_routes([
//     'route_path' => '!crud/edit?table=wa_campaign_items',
// ]);
// Route::additional_allowed_routes([
//     'route_path' => '!crud/delete?table=wa_campaign_items',
// ]);