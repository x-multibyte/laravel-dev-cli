<?php

namespace Tests\Unit\Services;

use LaravelDev\Services\PresetService;
use PHPUnit\Framework\TestCase;

class PresetServiceTest extends TestCase
{
    private PresetService $service;
    private string $cachePath;
    
    protected function setUp(): void
    {
        $this->cachePath = sys_get_temp_dir() . '/laravel-dev-presets-' . uniqid();
        mkdir($this->cachePath . '/api', 0755, true);
        mkdir($this->cachePath . '/framework', 0755, true);
        
        // Create sample preset files
        file_put_contents(
            $this->cachePath . '/api/12.json',
            json_encode([
                'name' => 'api/12',
                'version' => '1.0.0',
                'description' => 'Laravel 12 API',
                'laravel' => ['version' => '^12.0'],
                'dependencies' => ['composer' => ['laravel/sanctum' => '^4.0']],
            ])
        );
        
        file_put_contents(
            $this->cachePath . '/framework/12.json',
            json_encode([
                'name' => 'framework/12',
                'version' => '1.0.0',
                'description' => 'Laravel 12 Framework',
                'laravel' => ['version' => '^12.0'],
            ])
        );
        
        $this->service = new PresetService($this->cachePath);
    }
    
    protected function tearDown(): void
    {
        exec("rm -rf {$this->cachePath}");
    }
    
    public function test_list_returns_all_presets(): void
    {
        $presets = $this->service->list();
        
        $this->assertCount(2, $presets);
    }
    
    public function test_list_filters_by_category(): void
    {
        $presets = $this->service->list(category: 'api');
        
        $this->assertCount(1, $presets);
        $this->assertEquals('api/12', $presets[0]->name);
    }
    
    public function test_get_returns_preset(): void
    {
        $preset = $this->service->get('api/12');
        
        $this->assertNotNull($preset);
        $this->assertEquals('Laravel 12 API', $preset->description);
    }
    
    public function test_get_returns_null_for_unknown(): void
    {
        $preset = $this->service->get('unknown/preset');
        
        $this->assertNull($preset);
    }
}