<?php

$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();

$phinx = [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'database_migrations',
        'default_database' => 'local',
    ],
    'version_order' => 'creation',
];

// use the Slim settings for Phinx
$settings = require(__DIR__ . '/src/settings.php');

$phinx['environments'] = array_merge($phinx['environments'], $settings['database']);

return $phinx;
