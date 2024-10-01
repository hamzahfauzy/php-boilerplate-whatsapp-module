<?php

use Core\Event;
use Core\Page;
use Core\Request;
use Modules\Crud\Libraries\Repositories\CrudRepository;

// init table fields
$tableName  = 'wa_reply_settings';
$table      = tableFields($tableName);
$fields     = $table->getFields();
$module     = $table->getModule();
$title      = _ucwords(__("$module.label.$tableName"));
$error_msg  = get_flash_msg('error');
$success_msg  = get_flash_msg('success');
$old        = get_flash_msg('old');

$fields['expiration_message']['attr'] = [
    'class' => 'form-control select2-search__field'
];

$crudRepository = new CrudRepository($tableName);
$crudRepository->setModule($module);

$user_id = auth()->id;
$data = $crudRepository->find([
    'user_id' => $user_id
]);

if(Request::isMethod('POST'))
{
    $postData = isset($_POST[$tableName]) ? $_POST[$tableName] : [];
    $postData['user_id'] = $user_id;
    $postData['created_by'] = $user_id;
    if($data)
    {
        $data = $crudRepository->update($postData, [
            'user_id' => $user_id
        ]);
    }
    else
    {
        $create = $crudRepository->create($postData);
    }

    Event::trigger('crud/update/'.$module.'/'.$tableName, $create);

    set_flash_msg(['success'=>"$title berhasil diupdate"]);

    header('location:'.routeTo('whatsapp/autoreplies/setting'));
    die();
}

// page section
Page::setActive("$module.$tableName");
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
        'title' => __('crud.label.create')
    ]
]);

Page::pushHead('<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />');
Page::pushHead('<script src="https://cdn.tiny.cloud/1/rsb9a1wqmvtlmij61ssaqj3ttq18xdwmyt7jg23sg1ion6kn/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>');
Page::pushHead("<script>
tinymce.init({
  selector: 'textarea:not(.select2-search__field)',
  relative_urls : false,
  remove_script_host : false,
  convert_urls : true,
  plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
  toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
});
</script>");

Page::pushHead('<style>.select2,.select2-selection{height:38px!important;} .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:38px!important;}.select2-selection__arrow{height:34px!important;}</style>');
Page::pushFoot('<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>');
Page::pushFoot("<script src='".asset('assets/crud/js/crud.js')."'></script>");

Page::pushHook('create');

return view('whatsapp/views/autoreplies/setting', compact('fields', 'tableName', 'data', 'error_msg', 'success_msg', 'old'));