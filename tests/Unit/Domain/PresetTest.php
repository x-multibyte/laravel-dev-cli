<?php

namespace Tests\Unit\Domain;

use LaravelDev\Domain\Preset;
use PHPUnit\Framework\TestCase;

class PresetTest extends TestCase
{
    private array $sampleData;
    
    protected function setUp(): void
    {
        $this->sampleData = [
            'name' => 'api/12',
            'version' => '1.0.0',
            'description' => 'Laravel 12 API project',
            'laravel' => '^12.0',
            'dependencies' => [
                'composer' => ['laravel/sanctum' => '^4.0'],
                'npm' => [],
            ],
            'dev_dependencies' => [
                'composer' => [],
                'npm' => [],
            ],
            'env_template' => [
                'APP_NAME' => 'Laravel',
            ],
            'commands' => ['php artisan key:generate'],
            'hooks' => [
                'after_install' => ['php artisan migrate'],
            ],
            'metadata' => [
                'tags' => ['api', 'sanctum'],
                'category' => 'api',
            ],
        ];
    }
    
    public function test_from_array_creates_preset(): void
    {
        $preset = Preset::fromArray($this->sampleData);
        
        $this->assertEquals('api/12', $preset->name);
        $this->assertEquals('1.0.0', $preset->version);
        $this->assertEquals('Laravel 12 API project', $preset->description);
    }
    
    public function test_get_category(): void
    {
        $preset = Preset::fromArray($this->sampleData);
        
        $this->assertEquals('api', $preset->getCategory());
    }
    
    public function test_get_laravel_major_version(): void
    {
        $preset = Preset::fromArray($this->sampleData);
        
        $this->assertEquals(12, $preset->getLaravelMajorVersion());
    }
}