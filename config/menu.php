<?php 

return [
    [
        'label' => 'whatsapp.menu.devices',
        'icon'  => 'bx bx-devices',
        'route' => routeTo('crud/index',['table'=>'wa_devices']),
        'activeState' => 'whatsapp.wa_devices'
    ],
    [
        'label' => 'whatsapp.menu.contacts',
        'icon'  => 'bx bxs-contact',
        'route' => routeTo('crud/index',['table'=>'wa_contacts']),
        'activeState' => 'whatsapp.wa_contacts'
    ],
    [
        'label' => 'whatsapp.menu.templates',
        'icon'  => 'bx bx-list-ol',
        'route' => routeTo('crud/index',['table'=>'wa_templates']),
        'activeState' => 'whatsapp.wa_templates'
    ],
    [
        'label' => 'whatsapp.menu.messages',
        'icon'  => 'bx bx-message-square-dots',
        'activeState' => [
            'whatsapp.wa_messages',
            'whatsapp.send_message',
            'whatsapp.message_logs',
            'whatsapp.wa_campaigns',
            'whatsapp.wa_campaign_items',
        ],
        'items' => [
            [
                'label' => 'whatsapp.menu.send_message',
                'route' => routeTo('whatsapp/messages/send'),
                'activeState' => 'whatsapp.send_message',
            ],
            [
                'label' => 'whatsapp.menu.campaigns',
                'route' => routeTo('crud/index',['table' => 'wa_campaigns']),
                'activeState' => [
                    'whatsapp.wa_campaigns',
                    'whatsapp.wa_campaign_items'
                ]
            ],
            [
                'label' => 'whatsapp.menu.message_logs',
                'route' => routeTo('whatsapp/messages/logs'),
                'activeState' => 'whatsapp.message_logs',
            ],
        ]
    ],
    [
        'label' => 'whatsapp.menu.auto_reply',
        'icon'  => 'bx bx-transfer-alt',
        'activeState' => [
            'whatsapp.wa_auto_reply',
            'whatsapp.wa_reply_settings',
            'whatsapp.wa_replies',
        ],
        'items' => [
            [
                'label' => 'whatsapp.menu.setting',
                'route' => routeTo('whatsapp/autoreplies/setting'),
                'activeState' => 'whatsapp.wa_reply_settings',
            ],
            [
                'label' => 'whatsapp.menu.lists',
                'route' => routeTo('crud/index', ['table'=>'wa_replies']),
                'activeState' => 'whatsapp.wa_replies',
            ],
        ]
    ],
    [
        'label' => 'whatsapp.menu.documentations',
        'icon'  => 'bx bxs-file-blank',
        'activeState' => [
            'whatsapp.documentations',
            'whatsapp.api'
        ],
        'items' => [
            [
                'label' => 'whatsapp.menu.api',
                'route' => routeTo('whatsapp/documentations/api'),
                'activeState' => 'whatsapp.api',
            ]
        ]
    ],
];