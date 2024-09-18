<?php 

return [
    [
        'label' => 'whatsapp.menu.devices',
        'icon'  => 'bx bx-devices',
        'route' => routeTo('crud/index',['table'=>'wa_devices']),
        'activeState' => 'whatsapp.devices'
    ],
    [
        'label' => 'whatsapp.menu.contacts',
        'icon'  => 'bx bxs-contact',
        'route' => routeTo('crud/index',['table'=>'wa_contacts']),
        'activeState' => 'whatsapp.contacts'
    ],
    [
        'label' => 'whatsapp.menu.templates',
        'icon'  => 'bx bx-list-ol',
        'route' => routeTo('crud/index',['table'=>'wa_templates']),
        'activeState' => 'whatsapp.templates'
    ],
    [
        'label' => 'whatsapp.menu.messages',
        'icon'  => 'bx bx-message-square-dots',
        'activeState' => 'whatsapp.templates',
        'items' => [
            [
                'label' => 'whatsapp.menu.send_message',
                'route' => routeTo('whatsapp/messages/send'),
                'activeState' => 'whatsapp.send_message',
            ],
            [
                'label' => 'whatsapp.menu.message_logs',
                'route' => routeTo('whatsapp/messages/logs'),
                'activeState' => 'whatsapp.message_logs',
            ],
        ]
    ],
    [
        'label' => 'whatsapp.menu.documentations',
        'icon'  => 'bx bxs-file-blank',
        'activeState' => 'whatsapp.documentations',
        'items' => [
            [
                'label' => 'whatsapp.menu.api',
                'route' => routeTo('whatsapp/documentations/api'),
                'activeState' => 'whatsapp.api',
            ]
        ]
    ],
];