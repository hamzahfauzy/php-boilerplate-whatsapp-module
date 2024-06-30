<?php

use Core\Page;

Page::setTitle('API Documentation');
Page::setModuleName('API Documentation');
Page::setBreadcrumbs([
    [
        'url' => routeTo('/'),
        'title' => __('crud.label.home')
    ],
    [
        'title' => 'Documentations'
    ],
    [
        'title' => 'API'
    ]
]);

$documentations = [
    [
        'title' => 'Authentication',
        'items' => [
            [
                'title' => 'Get Authentication Bearer',
                'url'   => routeTo('api/auth/login'),
                'method' => 'POST',
                'header' => '',
                'param'  => [],
                'body' => [
                    'username' => '(Wajib) username yang biasa digunakan untuk login ke sistem',
                    'password' => '(Wajib) password yang biasa digunakan untuk login ke sistem'
                ]
            ]
        ]
    ],
    [
        'title' => 'Devices',
        'items' => [
            [
                'title' => 'Get',
                'url'   => routeTo('api/whatsapp/devices/get'),
                'method' => 'GET',
                'header' => 'Authorization: Bearer xxxx',
                'param'  => []
            ]
        ]
    ],
    [
        'title' => 'Contacts',
        'items' => [
            [
                'title' => 'Get',
                'url'   => routeTo('api/whatsapp/contacts/get'),
                'method' => 'GET',
                'header' => 'Authorization: Bearer xxxx',
                'param'  => []
            ]
        ]
    ],
    [
        'title' => 'Templates',
        'items' => [
            [
                'title' => 'Get',
                'url'   => routeTo('api/whatsapp/templates/get'),
                'method' => 'GET',
                'header' => 'Authorization: Bearer xxxx',
                'param'  => []
            ]
        ]
    ],
    [
        'title' => 'Messages',
        'items' => [
            [
                'title' => 'Send',
                'url'   => routeTo('api/whatsapp/messages/send'),
                'method' => 'POST',
                'header' => 'Authorization: Bearer xxxx',
                'param'  => [],
                'body'   => [
                    'device_id' => '(Wajib) dari devices',
                    'contact_id' => '(Wajib) dari contacts dan berupa array. contoh contact_id = [1,2,3]',
                    'template_id' => '(Opsional) dari templates',
                    'content' => '(Wajib jika template_id tidak ada) text',
                    'scheduled_at' => '(Opsional) di isi untuk mengatur pesan terjadwal'
                ]
            ],
            [
                'title' => 'Logs',
                'url'   => routeTo('api/whatsapp/messages/logs'),
                'method' => 'GET',
                'header' => 'Authorization: Bearer xxxx',
                'param'  => []
            ],
        ]
    ]
];


return view('whatsapp/views/documentations/api', compact('documentations'));