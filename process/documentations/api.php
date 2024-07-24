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
                    'phone' => '(Opsional) format dengan kode negara',
                    'contact_id' => '(Wajib jika phone kosong) dari contacts dan berupa array. contoh contact_id = [1,2,3]',
                    'template_id' => '(Opsional) dari templates',
                    'content' => '(Wajib jika template_id tidak ada) text',
                    'type' => '(Opsional) location, polling, media',
                    'location' => '(Wajib jika type location) array [lat, lng]',
                    'polling' => '(Wajib jika type polling) array [name, value = []]',
                    'media' => '(Wajib jika type media) array [name, url]',
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