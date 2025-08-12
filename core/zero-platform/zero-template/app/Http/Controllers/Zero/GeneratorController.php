<?php

namespace App\Http\Controllers\Zero;

use App\Http\Controllers\Controller;
use App\Services\YamlParserService;
use App\Services\AdvancedCodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use ZipArchive;

class GeneratorController extends Controller
{
    private $yamlParser;
    private $codeGenerator;

    public function __construct(YamlParserService $yamlParser, AdvancedCodeGeneratorService $codeGenerator)
    {
        $this->yamlParser = $yamlParser;
        $this->codeGenerator = $codeGenerator;
    }

    public function index()
    {
        $selectedTemplate = session('selectedTemplate');
        return view('zero.generator', compact('selectedTemplate'));
    }

    public function generate(Request $request)
    {
        try {
            $request->validate([
                'yaml_content' => 'required|string',
                'project_name' => 'nullable|string|max:255'
            ]);

            $yamlContent = $request->input('yaml_content');
            $projectName = $request->input('project_name');
            
            $parsedData = $this->yamlParser->parse($yamlContent);
            $generationResult = $this->codeGenerator->generatePlatform($parsedData, $projectName);

            if ($generationResult['success']) {
                $zipPath = $this->createProjectZip($generationResult);
                
                return response()->json([
                    'success' => true,
                    'message' => 'تم توليد المنصة بنجاح!',
                    'project_name' => $generationResult['project_name'],
                    'files_count' => count($generationResult['files_generated']),
                    'download_url' => route('zero.download', ['file' => basename($zipPath)]),
                    'files_generated' => $generationResult['files_generated']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في توليد المنصة',
                    'errors' => $generationResult['errors']
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء توليد المنصة: ' . $e->getMessage()
            ], 500);
        }
    }

    public function preview(Request $request)
    {
        try {
            $request->validate(['yaml_content' => 'required|string']);
            $yamlContent = $request->input('yaml_content');
            $parsedData = $this->yamlParser->parse($yamlContent);
            $preview = $this->generatePreview($parsedData);

            return response()->json(['success' => true, 'preview' => $preview]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إنشاء المعاينة: ' . $e->getMessage()
            ], 422);
        }
    }

    public function validate(Request $request)
    {
        try {
            $request->validate(['yaml_content' => 'required|string']);
            $yamlContent = $request->input('yaml_content');
            $this->yamlParser->parse($yamlContent);

            return response()->json(['success' => true, 'message' => 'YAML صحيح ومتوافق!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في YAML: ' . $e->getMessage(),
                'line' => $this->extractErrorLine($e->getMessage())
            ], 422);
        }
    }

    public function download(Request $request, $file)
    {
        $filePath = storage_path('downloads/' . $file);
        if (!File::exists($filePath)) {
            abort(404);
        }
        return Response::download($filePath)->deleteFileAfterSend();
    }

    private function createProjectZip(array $generationResult): string
    {
        $projectPath = $generationResult['project_path'];
        $projectName = $generationResult['project_name'];
        
        $downloadsPath = storage_path('downloads');
        if (!File::exists($downloadsPath)) {
            File::makeDirectory($downloadsPath, 0755, true);
        }

        $zipPath = $downloadsPath . '/' . $projectName . '_' . time() . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($projectPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($projectPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $readmeContent = $this->generateReadme($generationResult);
            $zip->addFromString('README.md', $readmeContent);
            $zip->close();
        }

        return $zipPath;
    }

    private function generatePreview(array $parsedData): array
    {
        $preview = [
            'platform_info' => $parsedData['platform_info'] ?? [],
            'database_summary' => [],
            'features_summary' => [],
            'estimated_files' => 0
        ];

        if (isset($parsedData['database_schema']['tables'])) {
            $tables = $parsedData['database_schema']['tables'];
            $preview['database_summary'] = [
                'tables_count' => count($tables),
                'tables' => array_keys($tables),
                'total_columns' => array_sum(array_map(function($table) {
                    return isset($table['columns']) ? count($table['columns']) : 0;
                }, $tables))
            ];
        }

        if (isset($parsedData['user_interface'])) {
            $ui = $parsedData['user_interface'];
            $preview['features_summary'] = [
                'theme' => $ui['theme'] ?? 'default',
                'rtl_support' => $ui['rtl_support'] ?? false,
                'multi_language' => $ui['multi_language'] ?? [],
                'accessibility' => $ui['accessibility'] ?? false
            ];
        }

        $tablesCount = count($parsedData['database_schema']['tables'] ?? []);
        $preview['estimated_files'] = ($tablesCount * 4) + 15;

        return $preview;
    }

    private function generateReadme(array $generationResult): string
    {
        $projectName = $generationResult['project_name'];
        $filesCount = count($generationResult['files_generated']);
        
        return "# {$projectName}

## مولد بواسطة Zero Platform

### معلومات المشروع
- **اسم المشروع**: {$projectName}
- **عدد الملفات المولدة**: {$filesCount}
- **تاريخ التوليد**: " . now()->format('Y-m-d H:i:s') . "

### تعليمات التشغيل
1. استخراج الملفات
2. تشغيل \`composer install\`
3. نسخ \`.env.example\` إلى \`.env\`
4. تشغيل \`php artisan key:generate\`
5. تشغيل \`php artisan migrate\`
6. تشغيل \`php artisan serve\`

### الدعم الفني
تم توليد هذا المشروع بواسطة Zero Platform - مولد المنصات الذكي.
";
    }

    private function extractErrorLine(string $errorMessage): ?int
    {
        if (preg_match('/line (\d+)/', $errorMessage, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
}
