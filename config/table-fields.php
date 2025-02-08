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
        'code' => [
            'type' => 'text',
            'label' => 'Code'
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
            'type' => 'datetime-local',
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
    ],
    'wa_campaigns' => [
        'user_id' => [
            'type' => 'options-obj:users,id,name',
            'label' => 'User'
        ],
        'title' => [
            'type' => 'text',
            'label' => 'Title',
            'attr' => [
                'required' => 'required'
            ]
        ],
        'device_id' => [
            'type' => 'options-obj:wa_devices,id,name',
            'label' => 'Device',
            'attr' => [
                'required' => 'required'
            ]
        ],
        'content' => [
            'type' => 'textarea',
            'label' => 'Content',
            'attr' => [
                'required' => 'required'
            ]
        ],
        'scheduled_at' => [
            'type' => 'datetime-local',
            'label' => 'Scheduled At'
        ],
        // 'start_at' => [
        //     'type' => 'datetime-local',
        //     'label' => 'Start At'
        // ],
        // 'finish_at' => [
        //     'type' => 'datetime-local',
        //     'label' => 'Finish At'
        // ],
        'expiring_time' => [
            'type' => 'number',
            'label' => 'Expiring Time',
            'attr' => [
                'required' => 'required'
            ]
        ],
    ],
    'wa_campaign_items' => [
        'campaign_id' => [
            'label' => 'Campaign',
            'type' => 'options-obj:wa_campaigns,id,title'
        ],
        'contact_code' => [
            'label' => 'Contact Code',
            'type' => 'text',
            'search' => [
                'wa_contacts.code',
            ],
        ],
        'contact_name' => [
            'label' => 'Contact',
            'type' => 'text',
            'search' => [
                'wa_contacts.name',
                'wa_contacts.phone',
            ],
        ],
        'content' => [
            'label' => 'Message',
            'type' => 'text',
            'search' => 'wa_messages.content'
        ],
        'response' => [
            'label' => 'Response',
            'type' => 'text',
            'search' => 'wa_campaign_items.response'
        ],
        'item_status' => [
            'label' => 'Status',
            'type' => 'text'
        ]
    ]
];