<?php

use Core\Database;
use Core\Event;
use Core\Page;
use Core\Request;

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
    'placeholder' => "Isi konten jika tidak memilih template\n NB : variabel yang tersedia : {contact.code}, {contact.name}, {contact.phone}"
];

$fields['import_contacts'] = [
    'label' => 'or Import Contact (<a href="/assets/whatsapp/format.xlsx">Download</a>)',
    'type' => 'file'
];

unset($fields['status']);

if(!$isSuperAdmin)
{
    $fields['device_id']['type'] .= '|user_id,'.auth()->id;
    $fields['template_id']['type'] .= '|user_id,'.auth()->id;
    $fields['contact_id']['type'] .= '|user_id,'.auth()->id;
}

if(Request::isMethod('POST'))
{
    $data = isset($_POST[$tableName]) ? $_POST[$tableName] : [];

    $import = $_FILES['import_contacts'];
    if(isset($import['name']) && !empty($import['name']))
    {
        $allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        
        if (!in_array($import['type'], $allowedTypes)) {
            set_flash_msg(['error'=> 'Silakan unggah file Excel']);
        }
        else
        {

            $fileExtension = pathinfo($import['name'], PATHINFO_EXTENSION);
            
            if (in_array($fileExtension, ['xls', 'xlsx'])) {
                $spreadsheet = IOFactory::load($import['tmp_name']);
                $sheet = $spreadsheet->getActiveSheet();
        
                foreach ($sheet->getRowIterator(2) as $row) {
                    $name = $sheet->getCell('B' . $row->getRowIndex())->getFormattedValue();
                    $phone = $sheet->getCell('C' . $row->getRowIndex())->getFormattedValue();
                    $code = $sheet->getCell('D' . $row->getRowIndex())->getFormattedValue();
                    
                    // check contacts
                    $contact = $db->single('wa_contacts', ['phone' => $phone, 'user_id' => $data['user_id']]);
                    if(!$contact)
                    {
                        $contact = $db->insert('wa_contacts', [
                            'code' => $code,
                            'name' => $name,
                            'phone' => $phone,
                            'user_id' => $data['user_id']
                        ]);
                    }
                    else
                    {
                        $db->update('wa_contacts', ['name' => $name], ['id' => $contact->id]);
                    }

                    $data['contacts'][] = $contact->id;
                }
            }
        }
    }

    $sendMessage = whatsappSendMessage($data, auth()->id);

    if(!$sendMessage['success'])
    {
        set_flash_msg(['error'=>$sendMessage['message']]);
        header("location: ".$_SERVER['HTTP_REFERER']);
        die();
    }

    set_flash_msg(['success'=>$sendMessage['message']]);

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