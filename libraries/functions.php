<?php

use Core\Database;
use Core\Event;

\Modules\Default\Libraries\Sdk\Dashboard::add('whatsappDashboardStatistic');

function whatsappDashboardStatistic()
{
    $db = new Database;
    $userId = auth()->id;
    $params = [
        'user_id' => $userId
    ];

    if(get_role($userId)->role_id == 1)
    {
        $params = [];
    }

    $data = [];
    $data['devices'] = $db->exists('wa_devices', $params);
    $data['contacts'] = $db->exists('wa_contacts', $params);
    $data['templates'] = $db->exists('wa_templates', $params);

    if(get_role($userId)->role_id != 1)
    {
        $params = [
            'created_by' => $userId
        ];
    }

    $data['message_in'] = $db->exists('wa_messages', array_merge([
        'record_type' => 'MESSAGE_IN'
    ], $params));
    $data['message_out'] = $db->exists('wa_messages', array_merge([
        'record_type' => 'MESSAGE_IN'
    ], $params));


    return view('whatsapp/views/dashboard/statistic', compact('data'));
}

function compileMessageContent($data, $content)
{
    foreach([
        'name' => '{contact.name}',
        'phone' => '{contact.phone}'
    ] as $key => $param)
    {
        $content = str_replace($param, $data[$key], $content);
    }

    return $content;
}

/**
 * @param : data {
 *          device_id, 
 *          template_id (optional), 
 *          scheduled_at (optional), 
 *          content (required if template_id exists),
 *          contact_id[]
 *         }, 
 * @param : user_id
 */
function whatsappSendMessage($data, $userId)
{
    // check device
    $db = new Database;

    $data['created_by'] = $userId;
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
        return [
            'success' => false,
            'message' => 'Konten atau template tidak boleh kosong'
        ];
    }

    if(empty($data['scheduled_at']))
    {
        unset($data['scheduled_at']);
    }

    $messages = [];
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
            $messages[] = $db->insert('wa_messages', $createData);
        }

    }

    $data['messages'] = $messages;

    Event::trigger('whatsapp/send_message', $data);

    return [
        'success' => true,
        'message' => 'Pesan berhasil disimpan'
    ];
}