<?php

return [
    'proxy_dir'       => PATH_APP.'proxies',
    'proxy_namespace' => '{{PROXY_PREFIX}}\Proxies',
    'config'          => [
        'development' => [
            'driver'   => 'pdo_mysql',
            'charset'  => 'utf8',
            'host'     => '{{HOST}}',
            'dbname'   => '{{DBNAME}}',
            'user'     => '{{USER}}',
            'password' => '{{PASS}}',
        ],
        'production'  => [
            'driver'   => 'pdo_mysql',
            'charset'  => 'utf8',
            'host'     => '127.0.0.1',
            'dbname'   => '',
            'user'     => '',
            'password' => '',
        ],
    ],
];
