<?php

use Illuminate\Support\Str;

return [
    'default' => env('DB_CONNECTION', 'sqlite'),
    
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('zero.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'options' => [
                PDO::ATTR_TIMEOUT => 30,
                PDO::ATTR_PERSISTENT => false,
            ],
            // Zero Platform Optimizations
            'pragma' => [
                'journal_mode' => 'WAL',
                'synchronous' => 'NORMAL',
                'cache_size' => -64000,
                'temp_store' => 'MEMORY',
                'mmap_size' => 268435456,
                'optimize' => true,
                'wal_autocheckpoint' => 1000,
                'foreign_keys' => 'ON',
                'recursive_triggers' => 'ON',
                'secure_delete' => 'FAST',
            ],
        ],
        
        // Zero Platform Template Database
        'zero_template' => [
            'driver' => 'sqlite',
            'database' => base_path('../../../core/templates/zero-base.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ],
    
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
    
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],
];
