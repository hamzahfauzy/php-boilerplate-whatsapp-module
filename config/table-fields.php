<?php 

return [
    'wa_devices' => [
        'user_id' => [
            'type' => 'options-obj:users,id,name',
            'label' => 'User'
        ],
        'name' => [
            'type' => 'text',
            'label' => 'Name'
        ],
        'phone' => [
            'type' => 'text',
            'label' => 'Phone'
        ],
        'status' => [
            'type' => 'text',
            'label' => 'Status'
        ],
        'webhook_url' => [
            'type' => 'text',
            'label' => 'Webhook URL'
        ],
    ],
    'wa_contacts' => [
        'user_id' => [
            'type' => 'options-obj:users,id,name',
            'label' => 'User'
        ],
        'name' => [
            'type' => 'text',
            'label' => 'Name'
        ],
        'phone' => [
            'type' => 'text',
            'label' => 'Phone'
        ],
    ],
    'wa_templates' => [
        'user_id' => [
            'type' => 'options-obj:users,id,name',
            'label' => 'User'
        ],
        'title' => [
            'type' => 'text',
            'label' => 'Title'
        ],
        'content' => [
            'type' => 'textarea',
            'label' => 'Content'
        ],
    ],
    'wa_messages' => [
        'device_id' => [
            'type' => 'options-obj:wa_devices,id,name',
            'label' => 'Device'
        ],
        'template_id' => [
            'type' => 'options-obj:wa_templates,id,title',
            'label' => 'Template'
        ],
        'contact_id' => [
            'type' => 'options-obj:wa_contacts,id,name',
            'label' => 'Contact'
        ],
        'content' => [
            'type' => 'textarea',
            'label' => 'Content'
        ],
        'record_type' => [
            'type' => 'text',
            'label' => 'Record Type'
        ],
        'created_at' => [
            'type' => 'date',
            'label' => 'Date'
        ]
    ],
];