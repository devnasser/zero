<?php

namespace Tests\Feature\Zero;

use Tests\TestCase;
use App\Services\Zero\Parser\YamlParser;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Zero Platform YAML Parser Test Suite
 * 
 * تم تطويره بواسطة السرب اللامحدود:
 * ⚙️ Gamma-97: استراتيجية الاختبار الشاملة والأمان
 * 👑 LEGEND-AI: تنسيق معايير الجودة
 * 🔧 Alpha-51: اختبار منطق المعالجة
 * 🎨 Beta-52: اختبار واجهات المستخدم
 */
class YamlParserTest extends TestCase
{
    use RefreshDatabase;

    protected YamlParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new YamlParser();
    }

    /**
     * Gamma-97 Security Test: Basic YAML parsing functionality
     */
    public function test_can_parse_valid_yaml(): void
    {
        $yamlContent = "
platform_info:
  name: 'Test Platform'
  type: 'web_application'
  description: 'Test Description'
database_schema:
  tables:
    users:
      columns:
        id:
          type: 'integer'
          primary: true
        name:
          type: 'string'
        email:
          type: 'string'
";

        $result = $this->parser->parse($yamlContent);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('platform_info', $result);
        $this->assertArrayHasKey('database_schema', $result);
        $this->assertEquals('Test Platform', $result['platform_info']['name']);
        $this->assertEquals('Zero_Platform_Unlimited_Swarm', $result['platform_info']['generated_by']);
    }

    /**
     * LEGEND-AI Strategy Test: Schema validation
     */
    public function test_validates_required_platform_info_fields(): void
    {
        $yamlContent = "
platform_info:
  name: 'Test Platform'
  type: 'web_application'
  # Missing description field
";

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing required platform info field: description');

        $this->parser->parse($yamlContent);
    }

    /**
     * Alpha-51 Processing Test: Database schema validation
     */
    public function test_validates_database_schema_structure(): void
    {
        $yamlContent = "
platform_info:
  name: 'Test Platform'
  type: 'web_application'
  description: 'Test Description'
database_schema:
  tables:
    users:
      columns:
        id:
          type: 'integer'
        name:
          # Missing type field
";

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Column name in table users must have type');

        $this->parser->parse($yamlContent);
    }

    /**
     * Gamma-97 Security Test: Injection attack prevention
     */
    public function test_prevents_code_injection_attacks(): void
    {
        $yamlContent = "
platform_info:
  name: 'eval(phpinfo())'
  type: 'web_application'
  description: 'Test Description'
";

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Security violation: Potentially dangerous content detected');

        $this->parser->parse($yamlContent);
    }

    /**
     * Beta-52 UI Test: User interface configuration
     */
    public function test_validates_user_interface_config(): void
    {
        $yamlContent = "
platform_info:
  name: 'Test Platform'
  type: 'web_application'
  description: 'Test Description'
user_interface:
  theme: 'modern'
  layout: 'responsive'
  rtl_support: true
  accessibility: true
";

        $result = $this->parser->parse($yamlContent);

        $this->assertArrayHasKey('user_interface', $result);
        $this->assertEquals('modern', $result['user_interface']['theme']);
        $this->assertTrue($result['user_interface']['rtl_support']);
        $this->assertTrue($result['user_interface']['accessibility']);
    }

    /**
     * LEGEND-AI Enhancement Test: Default values application
     */
    public function test_applies_intelligent_defaults(): void
    {
        $yamlContent = "
platform_info:
  name: 'Minimal Platform'
  type: 'web_application'
  description: 'Minimal Description'
";

        $result = $this->parser->parse($yamlContent);

        // Should have applied security defaults
        $this->assertArrayHasKey('security_config', $result);
        $this->assertTrue($result['security_config']['csrf_protection']);
        $this->assertEquals('AES-256', $result['security_config']['encryption']['algorithm']);

        // Should have applied localization defaults
        $this->assertArrayHasKey('localization', $result);
        $this->assertEquals('ar', $result['localization']['default_locale']);
        $this->assertContains('ar', $result['localization']['supported_locales']);
    }

    /**
     * Alpha-51 Optimization Test: Structure optimization
     */
    public function test_optimizes_structure_for_generation(): void
    {
        $yamlContent = "
localization:
  default_locale: 'en'
platform_info:
  name: 'Test Platform'
  type: 'web_application'
  description: 'Test Description'
database_schema:
  tables:
    users:
      columns:
        id:
          type: 'integer'
";

        $result = $this->parser->parse($yamlContent);

        // Should be ordered by priority
        $keys = array_keys($result);
        $this->assertEquals('platform_info', $keys[0]);
        $this->assertEquals('security_config', $keys[1]);
        $this->assertEquals('database_schema', $keys[2]);
    }

    /**
     * Gamma-97 Performance Test: Large YAML handling
     */
    public function test_handles_large_yaml_files(): void
    {
        $largeYaml = "
platform_info:
  name: 'Large Platform'
  type: 'web_application'
  description: 'Large Description'
database_schema:
  tables:
";

        // Generate 50 tables with 10 columns each
        for ($i = 1; $i <= 50; $i++) {
            $largeYaml .= "
    table_{$i}:
      columns:";
            for ($j = 1; $j <= 10; $j++) {
                $largeYaml .= "
        column_{$j}:
          type: 'string'";
            }
        }

        $startTime = microtime(true);
        $result = $this->parser->parse($largeYaml);
        $endTime = microtime(true);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('database_schema', $result);
        $this->assertCount(50, $result['database_schema']['tables']);
        
        // Should complete within reasonable time (less than 1 second)
        $this->assertLessThan(1.0, $endTime - $startTime);
    }

    /**
     * LEGEND-AI Integration Test: Complete workflow
     */
    public function test_complete_parsing_workflow(): void
    {
        $complexYaml = "
platform_info:
  name: 'Complete Platform'
  type: 'web_application'
  description: 'Complete test platform'
database_schema:
  tables:
    users:
      columns:
        id:
          type: 'integer'
          primary: true
        name:
          type: 'string'
        email:
          type: 'string'
      indexes:
        - ['email']
    posts:
      columns:
        id:
          type: 'integer'
          primary: true
        title:
          type: 'string'
        content:
          type: 'text'
        user_id:
          type: 'integer'
      relationships:
        - type: 'belongsTo'
          model: 'User'
user_interface:
  theme: 'professional'
  layout: 'sidebar'
  rtl_support: true
  accessibility: true
  components:
    - 'navigation'
    - 'breadcrumbs'
    - 'search'
business_logic:
  services:
    - 'UserService'
    - 'PostService'
  workflows:
    - 'user_registration'
    - 'post_creation'
integrations:
  saber:
    enabled: true
    config:
      api_key: 'test_key'
security_config:
  authentication:
    type: 'oauth'
  authorization:
    type: 'role_based'
  rate_limiting:
    enabled: true
    max_attempts: 100
localization:
  default_locale: 'ar'
  supported_locales: ['ar', 'en', 'fr']
";

        $result = $this->parser->parse($complexYaml);

        // Verify all sections are present and properly structured
        $this->assertArrayHasKey('platform_info', $result);
        $this->assertArrayHasKey('database_schema', $result);
        $this->assertArrayHasKey('user_interface', $result);
        $this->assertArrayHasKey('business_logic', $result);
        $this->assertArrayHasKey('integrations', $result);
        $this->assertArrayHasKey('security_config', $result);
        $this->assertArrayHasKey('localization', $result);

        // Verify data integrity
        $this->assertEquals('Complete Platform', $result['platform_info']['name']);
        $this->assertCount(2, $result['database_schema']['tables']);
        $this->assertEquals('professional', $result['user_interface']['theme']);
        $this->assertTrue($result['integrations']['saber']['enabled']);
        $this->assertEquals('oauth', $result['security_config']['authentication']['type']);
        $this->assertEquals('ar', $result['localization']['default_locale']);
    }
}
