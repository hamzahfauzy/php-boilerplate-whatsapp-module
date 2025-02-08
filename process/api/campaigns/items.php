<?php

use Core\Request;
use Core\Response;
use Modules\Crud\Libraries\Repositories\CrudRepository;

// init table fields
$tableName  = 'wa_campaign_items';
$table      = tableFields($tableName);
$fields     = $table->getFields();
$module     = $table->getModule();

// get data
$crudRepository = new CrudRepository($tableName);
$crudRepository->setModule($module);

// response 
// $order = Request::get('order', 'id');
// $sort  = Request::get('sort', 'asc');
// $limit = Request::get('limit', 20);
// $page  = Request::get('page', 1);
// $campaign_id = Request::get('campaign_id', 0);
// $limit = (($page-1)*$limit).','.$limit;

header('content-type:application/json');
return $crudRepository->dataTableApi($fields);
// return Response::json($crudRepository->get(['created_by' => auth()->id, 'campaign_id' => $campaign_id], [$order=>$sort], $limit), 'data '.$tableName.' retrieved');