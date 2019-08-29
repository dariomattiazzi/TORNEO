<?php
return [
    'db' => [
        'adapters' => [
            'dummy' => [
                'database' => 'torneo',
                'driver' => 'PDO_Mysql',
                'hostname' => '',
                'username' => 'root',
                'password' => 'frutill4s',
            ],
        ],
    ],
    'zf-mvc-auth' => [
        'authentication' => [
            'adapters' => [
                'torneo' => [
                    'adapter' => \ZF\MvcAuth\Authentication\OAuth2Adapter::class,
                    'storage' => [
                        'adapter' => \pdo::class,
                        'dsn' => 'mysql:host=localhost;dbname=torneo;',
                        'route' => '/oauth',
                        'username' => 'root',
                        'password' => 'frutill4s',
                    ],
                ],
            ],
        ],
    ],
];
