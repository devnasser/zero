<?php
/**
 * Smart Caching Configuration for Maximum Performance
 */
return [
    'cache' => [
        'default' => 'file',
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => '/workspace/cache/framework',
            ],
            'array' => [
                'driver' => 'array',
                'serialize' => false,
            ],
        ],
        'prefix' => 'zero_platform',
    ],
    'opcache' => [
        'enabled' => true,
        'preload' => true,
    ],
    'sqlite' => [
        'wal_mode' => true,
        'cache_size' => 64000,
        'mmap_size' => 268435456,
    ],
];
