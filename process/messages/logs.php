<?php

use Core\Page;
use Modules\Crud\Libraries\Repositories\CrudRepository;

// init table fields
$tableName  = 'wa_messages';
$table      = tableFields($tableName);
$fields     = $table->getFields();
$module     = $table->getModule();


// get data
$crudRepository = new CrudRepository($tableName);
$crudRepository->setModule($module);

if(isset($_GET['draw']))
{
    return $crudRepository->dataTable($fields);
}

// page section
$title = _ucwords(__("$module.label.$tableName"));
Page::setActive("whatsapp.message_logs");
Page::setTitle($title);
Page::setModuleName($title);
Page::setBreadcrumbs([
    [
        'url' => routeTo('/'),
        'title' => __('crud.label.home')
    ],
    [
        'url' => routeTo('crud/index', ['table' => $tableName]),
        'title' => $title
    ],
    [
        'title' => 'Index'
    ]
]);

Page::pushFoot("<script src='".asset('assets/crud/js/crud.js')."'></script>");

Page::pushHook('index');

return view('whatsapp/views/messages/logs', compact('fields', 'tableName', 'crudRepository'));