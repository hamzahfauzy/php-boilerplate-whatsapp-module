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
        'status' => [
            'type' => 'text',
            'label' => 'Status'
        ],
        'created_at' => [
            'type' => 'date',
            'label' => 'Date'
        ]
    ],
    'wa_reply_settings' => [
        'expiration_time' => [
            'type' => 'number',
            'label' => 'Expiration Time'
        ],
        'expiration_message' => [
            'type' => 'textarea',
            'label' => 'Expiration Message'
        ],
        'reply_status' => [
            'type' => 'options:ACTIVE|INACTIVE',
            'label' => 'Status'
        ],
    ],
    'wa_replies' => [
        'device_id' => [
            'type' => 'options-obj:wa_devices,id,name',
            'label' => 'Device'
        ],
        'keyword' => [
            'type' => 'textarea',
            'label' => 'Keyword'
        ],
        'content' => [
            'type' => 'textarea',
            'label' => 'Content'
        ],
        'reply_type' => [
            'type' => 'options:TEXT|WEBHOOK',
            'label' => 'Reply Type'
        ],
        'action_after' => [
            'type' => 'options:STAY|NEXT|BACK|EXIT',
            'label' => 'Action After'
        ],
    ]
];