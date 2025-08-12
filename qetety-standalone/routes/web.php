<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Zero Platform Web Routes
|--------------------------------------------------------------------------
|
| Routes للوصول إلى منصة Zero Platform
| تم تطويره بواسطة السرب اللامحدود
|
*/

// LEGEND-AI Strategy: Main platform routes
Route::get('/', function () {
    return redirect()->route('zero.dashboard');
})->name('home');

// Beta-52 UI Routes: Zero Platform Dashboard
Route::prefix('zero')->name('zero.')->group(function () {
    
    // Main Dashboard
    Route::get('/dashboard', function () {
        $stats = [
            'platforms' => 3,
            'active_projects' => 7,
            'templates' => 12,
            'success_rate' => '99.8'
        ];
        
        $recent_activities = [
            [
                'time' => 'قبل دقيقتين',
                'action' => 'إنشاء منصة جديدة',
                'project' => 'Auto Parts Marketplace',
                'status' => 'مكتمل',
                'status_color' => 'success'
            ],
            [
                'time' => 'قبل 5 دقائق',
                'action' => 'تحديث قالب',
                'project' => 'Restaurant Platform',
                'status' => 'جاري',
                'status_color' => 'warning'
            ],
            [
                'time' => 'قبل 10 دقائق',
                'action' => 'اختبار تلقائي',
                'project' => 'Education Platform',
                'status' => 'مكتمل',
                'status_color' => 'success'
            ]
        ];
        
        return view('zero.dashboard', compact('stats', 'recent_activities'));
    })->name('dashboard');
    
    // Generator Route
    Route::get('/generator', function () {
        return view('zero.generator');
    })->name('generator');
    
    // Templates Route
    Route::get('/templates', function () {
        return view('zero.templates');
    })->name('templates');
    
    // Projects Route
    Route::get('/projects', function () {
        return view('zero.projects');
    })->name('projects');
    
    // Profile Route
    Route::get('/profile', function () {
        return view('zero.profile');
    })->name('profile');
    
    // Settings Route
    Route::get('/settings', function () {
        return view('zero.settings');
    })->name('settings');
    
    // Documentation Route
    Route::get('/documentation', function () {
        return view('zero.documentation');
    })->name('documentation');
    
    // Alpha-51 API Routes: YAML Processing
    Route::post('/parse-yaml', function (Request $request) {
        try {
            $yamlContent = $request->input('yaml_content');
            $parser = new \App\Services\Zero\Parser\YamlParser();
            $result = $parser->parse($yamlContent);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'تم تحليل YAML بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'فشل في تحليل YAML'
            ], 400);
        }
    })->name('parse.yaml');
    
    // Alpha-51 + LEGEND-AI: Platform Generation
    Route::post('/generate-platform', function (Request $request) {
        try {
            $yamlContent = $request->input('yaml_content');
            $platformName = $request->input('platform_name', 'Generated Platform');
            
            $parser = new \App\Services\Zero\Parser\YamlParser();
            $generator = new \App\Services\Zero\Generator\CodeGenerator($parser);
            
            $result = $generator->generatePlatform($yamlContent, $platformName);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'تم توليد المنصة بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'فشل في توليد المنصة'
            ], 400);
        }
    })->name('generate.platform');
});

// Gamma-97 Security: Logout route
Route::post('/logout', function () {
    return redirect()->route('zero.dashboard');
})->name('logout');

// Beta-52 Enhancement: Welcome route with platform info
Route::get('/welcome', function () {
    return view('welcome', [
        'platform_name' => 'Zero Platform',
        'developed_by' => 'Unlimited Swarm',
        'version' => '1.0.0'
    ]);
})->name('welcome');

// Zero Platform Additional Routes
Route::get('/zero/templates', function () {
    return view('zero.templates');
})->name('zero.templates');

Route::get('/zero/projects', function () {
    $stats = [
        'active' => 3,
        'completed' => 7,
        'development' => 2,
        'total' => 12
    ];
    return view('zero.projects', compact('stats'));
})->name('zero.projects');

Route::get('/zero/settings', function () {
    return view('zero.settings');
})->name('zero.settings');

Route::get('/zero/documentation', function () {
    return view('zero.documentation');
})->name('zero.documentation');

// Advanced Generator Routes
Route::prefix('zero')->name('zero.')->group(function () {
    Route::post('/generate', [App\Http\Controllers\Zero\GeneratorController::class, 'generate'])->name('generate');
    Route::post('/preview', [App\Http\Controllers\Zero\GeneratorController::class, 'preview'])->name('preview');
    Route::post('/validate', [App\Http\Controllers\Zero\GeneratorController::class, 'validate'])->name('validate');
    Route::get('/download/{file}', [App\Http\Controllers\Zero\GeneratorController::class, 'download'])->name('download');
});
