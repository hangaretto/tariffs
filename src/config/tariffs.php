<?php

return [
    'middleware' => [
        'auth' => 'auth:api',
        'super_admin' => 'role:777'
    ],
    'controllers' => [
        'enabled' => true
    ],
    'notifications' => [
        'enabled' => true,
        'subject' => 'Уведомление с сайта - PiperCat',
        'templates' => [
            'buy' => [
                'template' => 'Покупка объекта - ":name", совершена успешно.',
                'action' => '-'
            ],
            'add_balance' => [
                'template' => 'Пополнение баланса.',
                'action' => '+'
            ],
        ]
    ]
];