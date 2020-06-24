<?php
require 'public/index.php';

return [
    'paths'        => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds'      => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_database' => 'development',
        'development'      => [
            'adapter' => 'mysql',
            'host' => $app->getContainer()->get('database.host'),
            'name' => $app->getContainer()->get('database.name'),
            'user' => $app->getContainer()->get('database.username'),
            'pass' => $app->getContainer()->get('database.password')
        ]
    ]
];