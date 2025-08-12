<?php
/**
 * Laravel Optimization Configuration
 * Optimized for Zero Platform Development
 */
return [
    'app' => [
        'debug' => false,
        'log_level' => 'error',
    ],
    'cache' => [
        'config' => true,
        'routes' => true,
        'views' => true,
        'events' => true,
    ],
    'database' => [
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:', // For testing
                'foreign_key_constraints' => true,
                'pragma' => [
                    'journal_mode' => 'WAL',
                    'synchronous' => 'NORMAL',
                    'cache_size' => -64000,
                    'temp_store' => 'MEMORY',
                    'mmap_size' => 268435456,
                ],
            ],
        ],
    ],
    'queue' => [
        'default' => 'sync',
    ],
    'session' => [
        'driver' => 'array',
    ],
];
