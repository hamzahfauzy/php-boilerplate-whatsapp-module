<?php 

return [
    [
        'label' => 'whatsapp.menu.devices',
        'icon'  => 'fa-fw fa-xl me-2 fa-solid fa-list',
        'route' => routeTo('crud/index',['table'=>'wa_devices']),
        'activeState' => 'whatsapp.devices'
    ],
    [
        'label' => 'whatsapp.menu.contacts',
        'icon'  => 'fa-fw fa-xl me-2 fa-solid fa-list',
        'route' => routeTo('crud/index',['table'=>'wa_contacts']),
        'activeState' => 'whatsapp.contacts'
    ],
    [
        'label' => 'whatsapp.menu.templates',
        'icon'  => 'fa-fw fa-xl me-2 fa-solid fa-list',
        'route' => routeTo('crud/index',['table'=>'wa_templates']),
        'activeState' => 'whatsapp.templates'
    ],
    [
        'label' => 'whatsapp.menu.messages',
        'icon'  => 'fa-fw fa-xl me-2 fa-solid fa-list',
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
];