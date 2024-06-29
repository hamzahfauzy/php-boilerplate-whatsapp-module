<?php

use Core\Database;
use Core\Event;
use Core\Page;
use Core\Request;
use Modules\Crud\Libraries\Repositories\CrudRepository;

// init table fields
$db = new Database;
$tableName  = 'wa_messages';
$table      = tableFields($tableName);
$fields     = $table->getFields();
$title      = 'Send Message';
$error_msg  = get_flash_msg('error');
$old        = get_flash_msg('old');
$success_msg = get_flash_msg('success');
$isSuperAdmin = get_role(auth()->id)->role_id == 1;

unset($fields['record_type']);
unset($fields['created_at']);

$fields['scheduled_at'] = [
    'type' => 'datetime-local',
    'label' => 'Schedule this message'
];

$fields['contact_id']['attr'] = [
    'multiple' => 'multiple'
];

$fields['content']['attr'] = [
    'placeholder' => "Isi konten jika tidak memilih template\n NB : variabel yang tersedia : {contact.name}, {contact.phone}"
];

if(!$isSuperAdmin)
{
    $fields['device_id']['type'] .= '|user_id,'.auth()->id;
    $fields['template_id']['type'] .= '|user_id,'.auth()->id;
    $fields['contact_id']['type'] .= '|user_id,'.auth()->id;
}

if(Request::isMethod('POST'))
{
    $data = isset($_POST[$tableName]) ? $_POST[$tableName] : [];

    // check device
    $isOwner = $db->exists('wa_devices', [
        'id' => $data['device_id'],
        'user_id' => auth()->id
    ]);

    if(!$isOwner && !$isSuperAdmin)
    {
        set_flash_msg(['error'=>'Unauthorized']);
        header("location: ".$_SERVER['HTTP_REFERER']);
        die();
    }

    $data['created_by'] = auth()->id;
    $content = $data['content'];
    if(empty($data['template_id']))
    {
        unset($data['template_id']);
    }
    else
    {
        $template = $db->single('wa_templates', [
            'id' => $data['template_id']
        ]);

        $content = $template->content;
    }

    if(empty($content))
    {
        set_flash_msg(['error'=>'Konten atau template tidak boleh kosong']);
        header("location: ".$_SERVER['HTTP_REFERER']);
        die();
    }

    if(empty($data['scheduled_at']))
    {
        unset($data['scheduled_at']);
    }

    foreach($data['contact_id'] as $contact_id)
    {
        $contact = $db->single('wa_contacts', [
            'id' => $contact_id
        ]);

        if($contact)
        {
            $createData = $data;
            $createData['contact_id'] = $contact_id;
            $createData['content'] = compileMessageContent((array) $contact, $content);
            $db->insert($tableName, $createData);
        }

    }
    // $crudRepository = new CrudRepository($tableName);
    // $crudRepository->setModule($module);
    // $create = $crudRepository->create($data);

    Event::trigger('whatsapp/send_message', $create);

    set_flash_msg(['success'=>"Pesan berhasil ditambahkan"]);

    header('location:'.routeTo('whatsapp/messages/send'));
    die();
}

// page section
Page::setActive("whatsapp.send_message");
Page::setTitle($title);
Page::setModuleName($title);
Page::setBreadcrumbs([
    [
        'url' => routeTo('/'),
        'title' => __('crud.label.home')
    ],
    [
        'title' => 'Send Message'
    ]
]);

Page::pushHead('<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />');
Page::pushHead('<style>.select2,.select2-selection{height:38px!important;} .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:38px!important;}.select2-selection__arrow{height:34px!important;}</style>');
Page::pushFoot('<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>');
Page::pushFoot("<script src='".asset('assets/crud/js/crud.js')."'></script>");

Page::pushHook('send_message');

return view('whatsapp/views/messages/send', compact('fields', 'tableName', 'success_msg','error_msg', 'old'));