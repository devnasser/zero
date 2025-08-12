<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],
        
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        
        // Zero Platform Custom Disks
        'zero_assets' => [
            'driver' => 'local',
            'root' => base_path('../../../assets'),
            'throw' => false,
        ],
        
        'zero_static' => [
            'driver' => 'local',
            'root' => base_path('../../../assets/static'),
            'url' => env('APP_URL').'/zero-assets',
            'visibility' => 'public',
            'throw' => false,
        ],
        
        'zero_cache' => [
            'driver' => 'local',
            'root' => base_path('../../../assets/cache'),
            'throw' => false,
        ],
        
        'zero_knowledge' => [
            'driver' => 'local',
            'root' => base_path('../../../knowledge'),
            'throw' => false,
        ],
    ],
    
    'links' => [
        public_path('storage') => storage_path('app/public'),
        public_path('zero-assets') => base_path('../../../assets/static'),
    ],
];
