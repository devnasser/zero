<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdvancedCodeGeneratorService
{
    private $outputPath;
    private $templatePath;
    private $generatedFiles = [];

    public function __construct()
    {
        $this->outputPath = storage_path('generated');
        $this->templatePath = resource_path('templates');
        
        // Ensure directories exist
        if (!File::exists($this->outputPath)) {
            File::makeDirectory($this->outputPath, 0755, true);
        }
    }

    /**
     * Generate complete platform from YAML specification
     */
    public function generatePlatform(array $yamlData, string $projectName = null): array
    {
        $projectName = $projectName ?: Str::slug($yamlData['platform_info']['name'] ?? 'new-platform');
        $projectPath = $this->outputPath . '/' . $projectName;

        // Create project directory
        File::makeDirectory($projectPath, 0755, true);

        $results = [
            'project_name' => $projectName,
            'project_path' => $projectPath,
            'files_generated' => [],
            'success' => true,
            'errors' => []
        ];

        try {
            // Generate Laravel project structure
            $this->generateLaravelStructure($projectPath, $yamlData);
            
            // Generate Models
            $this->generateModels($projectPath, $yamlData);
            
            // Generate Migrations
            $this->generateMigrations($projectPath, $yamlData);
            
            // Generate Controllers
            $this->generateControllers($projectPath, $yamlData);
            
            // Generate Views
            $this->generateViews($projectPath, $yamlData);
            
            // Generate Routes
            $this->generateRoutes($projectPath, $yamlData);
            
            // Generate Livewire Components
            $this->generateLivewireComponents($projectPath, $yamlData);
            
            // Generate API Resources
            $this->generateApiResources($projectPath, $yamlData);
            
            // Generate Services
            $this->generateServices($projectPath, $yamlData);
            
            // Generate Configuration Files
            $this->generateConfigFiles($projectPath, $yamlData);
            
            // Generate Language Files
            $this->generateLanguageFiles($projectPath, $yamlData);
            
            // Generate Package Files
            $this->generatePackageFiles($projectPath, $yamlData);

            $results['files_generated'] = $this->generatedFiles;
            
        } catch (\Exception $e) {
            $results['success'] = false;
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Generate Laravel project structure
     */
    private function generateLaravelStructure(string $projectPath, array $yamlData): void
    {
        $directories = [
            'app/Models',
            'app/Http/Controllers',
            'app/Http/Livewire',
            'app/Services',
            'app/Repositories',
            'database/migrations',
            'database/seeders',
            'resources/views',
            'resources/lang',
            'routes',
            'config',
            'public/css',
            'public/js',
            'public/images',
            'storage/app',
            'storage/logs'
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($projectPath . '/' . $dir, 0755, true);
        }

        // Generate composer.json
        $this->generateComposerJson($projectPath, $yamlData);
        
        // Generate .env file
        $this->generateEnvFile($projectPath, $yamlData);
        
        // Generate artisan file
        $this->generateArtisanFile($projectPath);
    }

    /**
     * Generate Models from database schema
     */
    private function generateModels(string $projectPath, array $yamlData): void
    {
        if (!isset($yamlData['database_schema']['tables'])) {
            return;
        }

        foreach ($yamlData['database_schema']['tables'] as $tableName => $tableConfig) {
            $modelName = Str::studly(Str::singular($tableName));
            $modelContent = $this->generateModelContent($modelName, $tableName, $tableConfig);
            
            $modelPath = $projectPath . '/app/Models/' . $modelName . '.php';
            File::put($modelPath, $modelContent);
            $this->generatedFiles[] = 'app/Models/' . $modelName . '.php';
        }
    }

    /**
     * Generate model content
     */
    private function generateModelContent(string $modelName, string $tableName, array $tableConfig): string
    {
        $fillable = [];
        $casts = [];
        
        if (isset($tableConfig['columns'])) {
            foreach ($tableConfig['columns'] as $columnName => $columnConfig) {
                if ($columnName !== 'id' && !str_ends_with($columnName, '_at')) {
                    $fillable[] = "'{$columnName}'";
                }
                
                // Add casts for specific types
                if (isset($columnConfig['type'])) {
                    switch ($columnConfig['type']) {
                        case 'boolean':
                            $casts[] = "'{$columnName}' => 'boolean'";
                            break;
                        case 'decimal':
                            $casts[] = "'{$columnName}' => 'decimal:2'";
                            break;
                        case 'date':
                            $casts[] = "'{$columnName}' => 'date'";
                            break;
                        case 'timestamp':
                            $casts[] = "'{$columnName}' => 'datetime'";
                            break;
                    }
                }
            }
        }

        $fillableString = implode(",\n        ", $fillable);
        $castsString = implode(",\n        ", $casts);

        return "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    use HasFactory;

    protected \$table = '{$tableName}';

    protected \$fillable = [
        {$fillableString}
    ];

    protected \$casts = [
        {$castsString}
    ];

    // Relationships will be added here based on foreign keys
    
    // Scopes
    public function scopeActive(\$query)
    {
        return \$query->where('active', true);
    }
}";
    }

    /**
     * Generate Migrations
     */
    private function generateMigrations(string $projectPath, array $yamlData): void
    {
        if (!isset($yamlData['database_schema']['tables'])) {
            return;
        }

        $timestamp = now()->format('Y_m_d_His');
        $counter = 1;

        foreach ($yamlData['database_schema']['tables'] as $tableName => $tableConfig) {
            $migrationTimestamp = now()->addSeconds($counter)->format('Y_m_d_His');
            $migrationName = "create_{$tableName}_table";
            $migrationContent = $this->generateMigrationContent($tableName, $tableConfig);
            
            $migrationPath = $projectPath . "/database/migrations/{$migrationTimestamp}_{$migrationName}.php";
            File::put($migrationPath, $migrationContent);
            $this->generatedFiles[] = "database/migrations/{$migrationTimestamp}_{$migrationName}.php";
            
            $counter++;
        }
    }

    /**
     * Generate migration content
     */
    private function generateMigrationContent(string $tableName, array $tableConfig): string
    {
        $className = 'Create' . Str::studly($tableName) . 'Table';
        $columns = [];

        if (isset($tableConfig['columns'])) {
            foreach ($tableConfig['columns'] as $columnName => $columnConfig) {
                $type = $columnConfig['type'] ?? 'string';
                $nullable = isset($columnConfig['nullable']) && $columnConfig['nullable'] ? '->nullable()' : '';
                $primary = isset($columnConfig['primary']) && $columnConfig['primary'] ? '' : '';
                
                switch ($type) {
                    case 'integer':
                        if ($primary) {
                            $columns[] = "\$table->id('{$columnName}');";
                        } else {
                            $columns[] = "\$table->integer('{$columnName}'){$nullable};";
                        }
                        break;
                    case 'string':
                        $length = $columnConfig['length'] ?? 255;
                        $columns[] = "\$table->string('{$columnName}', {$length}){$nullable};";
                        break;
                    case 'text':
                        $columns[] = "\$table->text('{$columnName}'){$nullable};";
                        break;
                    case 'decimal':
                        $precision = $columnConfig['precision'] ?? 8;
                        $scale = $columnConfig['scale'] ?? 2;
                        $columns[] = "\$table->decimal('{$columnName}', {$precision}, {$scale}){$nullable};";
                        break;
                    case 'boolean':
                        $default = isset($columnConfig['default']) ? "->default({$columnConfig['default']})" : '';
                        $columns[] = "\$table->boolean('{$columnName}'){$nullable}{$default};";
                        break;
                    case 'date':
                        $columns[] = "\$table->date('{$columnName}'){$nullable};";
                        break;
                    case 'timestamp':
                        $columns[] = "\$table->timestamp('{$columnName}'){$nullable};";
                        break;
                    case 'enum':
                        $values = isset($columnConfig['values']) ? "['" . implode("', '", $columnConfig['values']) . "']" : "['active', 'inactive']";
                        $columns[] = "\$table->enum('{$columnName}', {$values}){$nullable};";
                        break;
                    default:
                        $columns[] = "\$table->string('{$columnName}'){$nullable};";
                }
            }
        }

        // Add timestamps if not already defined
        if (!isset($tableConfig['columns']['created_at']) && !isset($tableConfig['columns']['updated_at'])) {
            $columns[] = "\$table->timestamps();";
        }

        $columnsString = implode("\n            ", $columns);

        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            {$columnsString}
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
};";
    }

    /**
     * Generate Controllers
     */
    private function generateControllers(string $projectPath, array $yamlData): void
    {
        if (!isset($yamlData['database_schema']['tables'])) {
            return;
        }

        foreach ($yamlData['database_schema']['tables'] as $tableName => $tableConfig) {
            $modelName = Str::studly(Str::singular($tableName));
            $controllerName = $modelName . 'Controller';
            $controllerContent = $this->generateControllerContent($controllerName, $modelName, $tableName);
            
            $controllerPath = $projectPath . '/app/Http/Controllers/' . $controllerName . '.php';
            File::put($controllerPath, $controllerContent);
            $this->generatedFiles[] = 'app/Http/Controllers/' . $controllerName . '.php';
        }

        // Generate main dashboard controller
        $dashboardContent = $this->generateDashboardController($yamlData);
        $dashboardPath = $projectPath . '/app/Http/Controllers/DashboardController.php';
        File::put($dashboardPath, $dashboardContent);
        $this->generatedFiles[] = 'app/Http/Controllers/DashboardController.php';
    }

    /**
     * Generate controller content
     */
    private function generateControllerContent(string $controllerName, string $modelName, string $tableName): string
    {
        $modelVariable = Str::camel($modelName);
        $viewPrefix = Str::kebab($tableName);

        return "<?php

namespace App\Http\Controllers;

use App\Models\\{$modelName};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class {$controllerName} extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request \$request)
    {
        \$query = {$modelName}::query();
        
        // Search functionality
        if (\$request->has('search')) {
            \$search = \$request->get('search');
            \$query->where('name', 'like', \"%{\$search}%\");
        }
        
        // Pagination
        \${$modelVariable}s = \$query->paginate(15);
        
        return view('{$viewPrefix}.index', compact('{$modelVariable}s'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('{$viewPrefix}.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request \$request)
    {
        \$validator = Validator::make(\$request->all(), [
            'name' => 'required|string|max:255',
            // Add more validation rules based on model fields
        ]);

        if (\$validator->fails()) {
            return redirect()->back()
                ->withErrors(\$validator)
                ->withInput();
        }

        {$modelName}::create(\$request->validated());

        return redirect()->route('{$viewPrefix}.index')
            ->with('success', 'تم إنشاء العنصر بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show({$modelName} \${$modelVariable})
    {
        return view('{$viewPrefix}.show', compact('{$modelVariable}'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit({$modelName} \${$modelVariable})
    {
        return view('{$viewPrefix}.edit', compact('{$modelVariable}'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request \$request, {$modelName} \${$modelVariable})
    {
        \$validator = Validator::make(\$request->all(), [
            'name' => 'required|string|max:255',
            // Add more validation rules
        ]);

        if (\$validator->fails()) {
            return redirect()->back()
                ->withErrors(\$validator)
                ->withInput();
        }

        \${$modelVariable}->update(\$request->validated());

        return redirect()->route('{$viewPrefix}.index')
            ->with('success', 'تم تحديث العنصر بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy({$modelName} \${$modelVariable})
    {
        \${$modelVariable}->delete();

        return redirect()->route('{$viewPrefix}.index')
            ->with('success', 'تم حذف العنصر بنجاح');
    }
}";
    }

    /**
     * Generate dashboard controller
     */
    private function generateDashboardController(array $yamlData): string
    {
        $platformName = $yamlData['platform_info']['name'] ?? 'منصتي';
        
        return "<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        \$stats = [
            'platform_name' => '{$platformName}',
            'total_users' => DB::table('users')->count(),
            'active_sessions' => 0, // Implement session tracking
            'system_status' => 'نشط',
            'last_updated' => now()
        ];

        return view('dashboard.index', compact('stats'));
    }
}";
    }

    /**
     * Generate composer.json file
     */
    private function generateComposerJson(string $projectPath, array $yamlData): void
    {
        $platformName = $yamlData['platform_info']['name'] ?? 'منصتي';
        $description = $yamlData['platform_info']['description'] ?? 'منصة مولدة بواسطة Zero Platform';
        
        $composerData = [
            'name' => Str::slug($platformName) . '/platform',
            'description' => $description,
            'type' => 'project',
            'require' => [
                'php' => '^8.4',
                'laravel/framework' => '^11.0',
                'livewire/livewire' => '^3.0',
                'spatie/laravel-permission' => '^6.0'
            ],
            'require-dev' => [
                'laravel/pint' => '^1.0',
                'pestphp/pest' => '^2.0',
                'pestphp/pest-plugin-laravel' => '^2.0'
            ],
            'autoload' => [
                'psr-4' => [
                    'App\\' => 'app/',
                    'Database\\Factories\\' => 'database/factories/',
                    'Database\\Seeders\\' => 'database/seeders/'
                ]
            ],
            'scripts' => [
                'test' => 'pest',
                'format' => 'pint'
            ],
            'config' => [
                'optimize-autoloader' => true,
                'preferred-install' => 'dist',
                'sort-packages' => true
            ]
        ];

        File::put($projectPath . '/composer.json', json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->generatedFiles[] = 'composer.json';
    }

    /**
     * Generate .env file
     */
    private function generateEnvFile(string $projectPath, array $yamlData): void
    {
        $platformName = $yamlData['platform_info']['name'] ?? 'منصتي';
        $dbName = Str::slug($platformName) . '_db';
        
        $envContent = "APP_NAME=\"{$platformName}\"
APP_ENV=local
APP_KEY=base64:" . base64_encode(Str::random(32)) . "
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=" . storage_path("app/{$dbName}.sqlite") . "

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=\"hello@example.com\"
MAIL_FROM_NAME=\"\${APP_NAME}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME=\"\${APP_NAME}\"
VITE_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"
VITE_PUSHER_HOST=\"\${PUSHER_HOST}\"
VITE_PUSHER_PORT=\"\${PUSHER_PORT}\"
VITE_PUSHER_SCHEME=\"\${PUSHER_SCHEME}\"
VITE_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"";

        File::put($projectPath . '/.env', $envContent);
        $this->generatedFiles[] = '.env';
    }

    // Additional generation methods...
    private function generateViews(string $projectPath, array $yamlData): void
    {
        // Generate basic view structure
        // This would be implemented with more detailed view generation
    }

    private function generateRoutes(string $projectPath, array $yamlData): void
    {
        // Generate web.php and api.php routes
        // This would be implemented with proper route generation
    }

    private function generateLivewireComponents(string $projectPath, array $yamlData): void
    {
        // Generate Livewire components for interactive features
    }

    private function generateApiResources(string $projectPath, array $yamlData): void
    {
        // Generate API resources and transformers
    }

    private function generateServices(string $projectPath, array $yamlData): void
    {
        // Generate business logic services
    }

    private function generateConfigFiles(string $projectPath, array $yamlData): void
    {
        // Generate Laravel config files
    }

    private function generateLanguageFiles(string $projectPath, array $yamlData): void
    {
        // Generate language files for multi-language support
    }

    private function generatePackageFiles(string $projectPath, array $yamlData): void
    {
        // Generate artisan, package.json, etc.
    }

    private function generateArtisanFile(string $projectPath): void
    {
        $artisanContent = "#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

\$app = require_once __DIR__.'/bootstrap/app.php';

\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);

\$status = \$kernel->handle(
    \$input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

\$kernel->terminate(\$input, \$status);

exit(\$status);";

        File::put($projectPath . '/artisan', $artisanContent);
        chmod($projectPath . '/artisan', 0755);
        $this->generatedFiles[] = 'artisan';
    }
}
