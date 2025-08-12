<?php

use Illuminate\Support\Str;

return [
    'default' => env('CACHE_STORE', 'database'),
    
    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
        
        'database' => [
            'driver' => 'database',
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'connection' => null,
            'lock_connection' => null,
        ],
        
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],
        
        // Zero Platform Cache Stores
        'zero_file' => [
            'driver' => 'file',
            'path' => base_path('../../../assets/cache/data'),
            'lock_path' => base_path('../../../assets/cache/locks'),
        ],
        
        'zero_template' => [
            'driver' => 'file',
            'path' => base_path('../../../assets/cache/templates'),
            'lock_path' => base_path('../../../assets/cache/locks'),
        ],
        
        'zero_knowledge' => [
            'driver' => 'file',
            'path' => base_path('../../../assets/cache/knowledge'),
            'lock_path' => base_path('../../../assets/cache/locks'),
        ],
        
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],
        
        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],
        
        'octane' => [
            'driver' => 'octane',
        ],
    ],
    
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
];
