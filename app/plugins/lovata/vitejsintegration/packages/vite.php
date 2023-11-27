<?php

return [
    'configs' => [
        'default' => [
            'entrypoints' => [
                'ignore' => '/\\.(d\\.ts|json)$/',
            ],
            'dev_server' => [
                'enabled' => true,
                'url' => env('VITE_DEV_SERVER_URL', 'http://0.0.0.0:5173'),
                'ping_before_using_manifest' => true,
                'ping_url' => null,
                'ping_timeout' => 1,
                'key'  => env('VITE_DEV_SERVER_KEY'),
                'cert' => env('VITE_DEV_SERVER_CERT'),
            ]
        ],
    ],

    'aliases' => [
        '@' => ''
    ],

    'commands' => [
        'artisan' => [
            'vite:tsconfig',
        ],
        'shell' => [
            //
        ],
    ],

    'testing' => [
        'use_manifest' => false,
    ],

    'env_prefixes' => ['VITE_', 'MIX_', 'SCRIPT_'],

    'interfaces' => [
        'heartbeat_checker' => Innocenzi\Vite\HeartbeatCheckers\HttpHeartbeatChecker::class,
        'tag_generator' => Innocenzi\Vite\TagGenerators\CallbackTagGenerator::class,
        'entrypoints_finder' => \Lovata\ViteJsintegration\Classes\OctoberEntrypointsFinder::class,
    ],

    'default' => env('VITE_DEFAULT_CONFIG', 'default'),
];
