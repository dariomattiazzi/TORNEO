<?php
return [
    'zf-content-negotiation' => [
        'selectors' => [],
    ],
    'db' => [
        'adapters' => [
            'dummy' => [],
        ],
    ],
    'router' => [
        'routes' => [
            'oauth' => [
                'options' => [
                    'spec' => '%oauth%',
                    'regex' => '(?P<oauth>(/oauth))',
                ],
                'type' => 'regex',
            ],
        ],
    ],
    'zf-mvc-auth' => [
        'authentication' => [
            'map' => [],
        ],
    ],
    'zf-oauth2' => [
        'access_lifetime' => 18000,
        'options' => [
            'refresh_token_lifetime' => 604800,
            'always_issue_new_refresh_token' => true,
        ],
    ],
];
