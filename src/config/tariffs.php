<?php

return [
    'middleware' => [
        'auth' => 'auth:api',
        'super_admin' => 'auth:api'
    ],
    'controllers' => [
        'enabled' => true
    ],
    'services' => [
        'yandex' => [
            'testmode' => true,
            'shopId' => 160091,
            'shopPassword' => '123123',
            'scid' => 558710,
            'currency' => 10643,
            'certPassword' => '123123',
            'receipt_text' => 'Аванс за использование сервиса',
        ]
    ],
    'billing' => [
        'notifications' => true,
        'subject' => 'Уведомление с сайта - PiperCat',
        'templates' => [
            'buy' => [
                'template' => 'Покупка объекта - ":name", совершена успешно.',
                'action' => '-',
                'enabled' => true,
                'notification' => true,
            ],
            'daily_buy' => [
                'template' => 'Ежедневное списание.',
                'action' => '-',
                'enabled' => true,
                'notification' => true,
            ],
            'add_balance' => [
                'template' => 'Пополнение баланса.',
                'action' => '+',
                'enabled' => true,
                'notification' => true,
            ],

            // ниже можно добавить свои шаблоны

        ]
    ]
];