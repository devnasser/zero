<?php

namespace App\Services\Zero\Parser;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Yaml\Yaml;
use Exception;

/**
 * Zero Platform YAML Parser Engine
 * 
 * تم تطويره بتعاون السرب اللامحدود:
 * 👑 LEGEND-AI: تصميم المعمارية والاستراتيجية الشاملة
 * 🔧 Alpha-51: تطوير منطق المعالجة والتحليل المتقدم
 * 🎨 Beta-52: تصميم واجهة التفاعل مع المولد
 * ⚙️ Gamma-97: تحسين الأداء والأمان المتقدم
 */
class YamlParser
{
    protected array $allowedSchemas = [
        'platform_info',
        'database_schema', 
        'user_interface',
        'business_logic',
        'integrations',
        'security_config',
        'localization'
    ];

    public function parse(string $yamlContent): array
    {
        try {
            Log::info('Zero Platform: YAML parsing initiated by Unlimited Swarm');
            
            $parsedData = Yaml::parse($yamlContent);
            
            if (!is_array($parsedData)) {
                throw new Exception('Invalid YAML structure: Expected array format');
            }

            $this->validateSecurityConstraints($parsedData);
            $validatedData = $this->validateSchema($parsedData);
            $enrichedData = $this->enrichWithDefaults($validatedData);
            $optimizedData = $this->optimizeStructure($enrichedData);
            
            Log::info('Zero Platform: YAML parsing completed successfully', [
                'sections_count' => count($optimizedData),
                'processed_by' => 'Unlimited_Swarm_Collaboration'
            ]);
            
            return $optimizedData;
            
        } catch (Exception $e) {
            Log::error('Zero Platform: YAML parsing failed', [
                'error' => $e->getMessage(),
                'handled_by' => 'Alpha-51_Error_Recovery'
            ]);
            
            throw new Exception("YAML parsing failed: " . $e->getMessage());
        }
    }

    protected function validateSchema(array $data): array
    {
        $validatedSections = [];
        
        foreach ($data as $sectionName => $sectionData) {
            if (in_array($sectionName, $this->allowedSchemas)) {
                $validatedSections[$sectionName] = $this->validateSection($sectionName, $sectionData);
            }
        }
        
        return $validatedSections;
    }

    protected function validateSection(string $sectionName, mixed $sectionData): array
    {
        return match($sectionName) {
            'platform_info' => $this->validatePlatformInfo($sectionData),
            'database_schema' => $this->validateDatabaseSchema($sectionData),
            'user_interface' => $this->validateUserInterface($sectionData),
            default => is_array($sectionData) ? $sectionData : []
        };
    }

    protected function validatePlatformInfo(mixed $data): array
    {
        if (!is_array($data)) {
            throw new Exception('Platform info must be an array');
        }
        
        $required = ['name', 'type', 'description'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new Exception("Missing required platform info field: {$field}");
            }
        }
        
        $data['generated_by'] = 'Zero_Platform_Unlimited_Swarm';
        $data['generation_timestamp'] = now()->toISOString();
        $data['version'] = '1.0.0';
        
        return $data;
    }

    protected function validateDatabaseSchema(mixed $data): array
    {
        if (!is_array($data) || !isset($data['tables'])) {
            throw new Exception('Database schema must contain tables array');
        }
        
        return ['tables' => $data['tables']];
    }

    protected function validateUserInterface(mixed $data): array
    {
        if (!is_array($data)) {
            throw new Exception('User interface config must be an array');
        }
        
        return [
            'theme' => $data['theme'] ?? 'default',
            'layout' => $data['layout'] ?? 'responsive',
            'components' => $data['components'] ?? [],
            'rtl_support' => $data['rtl_support'] ?? true,
            'accessibility' => $data['accessibility'] ?? true
        ];
    }

    protected function validateSecurityConstraints(array $data): void
    {
        $jsonString = json_encode($data);
        $dangerousPatterns = ['/eval\s*\(/', '/exec\s*\(/', '/system\s*\(/'];
        
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $jsonString)) {
                throw new Exception('Security violation: Potentially dangerous content detected');
            }
        }
    }

    protected function enrichWithDefaults(array $data): array
    {
        if (!isset($data['platform_info'])) {
            $data['platform_info'] = [
                'name' => 'Generated Platform',
                'type' => 'web_application',
                'description' => 'Generated by Zero Platform',
                'version' => '1.0.0'
            ];
        }
        
        return $data;
    }

    protected function optimizeStructure(array $data): array
    {
        $priorityOrder = ['platform_info', 'database_schema', 'user_interface'];
        $optimized = [];
        
        foreach ($priorityOrder as $section) {
            if (isset($data[$section])) {
                $optimized[$section] = $data[$section];
            }
        }
        
        return $optimized;
    }
}
