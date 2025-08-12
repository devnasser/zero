<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Zero Platform Structure Configuration
    |--------------------------------------------------------------------------
    */
    
    'paths' => [
        'core' => base_path('../../../core'),
        'knowledge' => base_path('../../../knowledge'),
        'assets' => base_path('../../../assets'),
        'tools' => base_path('../../../tools'),
        'templates' => base_path('../../../core/templates'),
    ],
    
    'assets' => [
        'static' => base_path('../../../assets/static'),
        'generated' => base_path('../../../assets/generated'),
        'cache' => base_path('../../../assets/cache'),
    ],
    
    'knowledge' => [
        'ai_transfer' => base_path('../../../knowledge/ai-transfer'),
        'docs' => base_path('../../../knowledge/docs-archive'),
        'research' => base_path('../../../knowledge/research'),
    ],
    
    'tools' => [
        'scripts' => base_path('../../../tools/scripts'),
        'utilities' => base_path('../../../tools/utilities'),
        'benchmarks' => base_path('../../../tools/benchmarks'),
    ],
];
