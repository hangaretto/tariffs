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
            'shopId' => 111,
            'scid' => 111,
            'receipt_text' => 'Аванс за использование сервиса'
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