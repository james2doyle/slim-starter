<?php

return [
    'settings' => [
        'public_path' => __DIR__ . '/../public/',
        'displayErrorDetails' => getenv('APP_DEBUG'), // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => sprintf('[%s] %s', getenv('APP_ENV'), getenv('APP_NAME')),
            'path' => !empty(getenv('DOCKER')) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
    'database' => [
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testing_slim',
            'user' => 'root',
            'pass' => 'root',
            'port' => '3306',
            'charset' => 'utf8mb4',
        ],
        'local' => [
            'adapter' => getenv('DB_ADAPTER'),
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'pass' => getenv('DB_PASS'),
            'port' => getenv('DB_PORT'),
            'charset' => getenv('DB_CHARSET'),
        ],
    ],
];
