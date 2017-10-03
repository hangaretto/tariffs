<?php

return [
    'middleware' => [
        'auth' => 'auth:api',
        'super_admin' => 'role:777'
    ],
    'controllers' => [
        'enabled' => true
    ]
];