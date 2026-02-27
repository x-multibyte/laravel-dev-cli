<?php

namespace XMultibyte\LaravelDev\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use XMultibyte\LaravelDev\Services\PresetConfig;

class PresetConfigTest extends TestCase
{
    private string $testConfigDir;
    private PresetConfig $config;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->testConfigDir = sys_get_temp_dir() . '/laravel-dev-test-' . uniqid();
        mkdir($this->testConfigDir . '/config', 0755, true);
        $this->config = new PresetConfig($this->testConfigDir);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        if (is_dir($this->testConfigDir)) {
            $this->removeDirectory($this->testConfigDir);
        }
    }
    
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $items = scandir($dir);
        if ($items === false) {
            return;
        }
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        rmdir($dir);
    }
    
    public function test_get_default_repository(): void
    {
        $this->assertEquals(
            'https://github.com/x-multibyte/laravel-dev-presets.git',
            $this->config->get('repository')
        );
    }
    
    public function test_get_default_branch(): void
    {
        $this->assertEquals('main', $this->config->get('branch'));
    }
    
    public function test_get_with_env_override(): void
    {
        putenv('LARAVEL_DEV_PRESETS_REPO=https://github.com/custom/presets.git');
        
        $this->assertEquals(
            'https://github.com/custom/presets.git',
            $this->config->get('repository')
        );
        
        // Cleanup
        putenv('LARAVEL_DEV_PRESETS_REPO');
    }
    
    public function test_set_and_save(): void
    {
        $this->config->set('branch', 'develop');
        $this->config->save();
        
        $this->assertEquals('develop', $this->config->get('branch'));
        
        // Verify file was created
        $configFile = $this->testConfigDir . '/config/presets.json';
        $this->assertFileExists($configFile);
    }
    
    public function test_all_returns_all_config(): void
    {
        $all = $this->config->all();
        
        $this->assertArrayHasKey('repository', $all);
        $this->assertArrayHasKey('branch', $all);
    }
    
    public function test_get_non_existing_key_returns_default(): void
    {
        $this->assertNull($this->config->get('non_existing_key'));
        $this->assertEquals('default_value', $this->config->get('non_existing_key', 'default_value'));
    }
    
    public function test_reset_clears_loaded_config(): void
    {
        $this->config->set('branch', 'develop');
        $this->assertEquals('develop', $this->config->get('branch'));
        
        $this->config->reset();
        
        // After reset, should return default value since config wasn't saved
        $this->assertEquals('main', $this->config->get('branch'));
    }
    
    public function test_save_includes_last_updated(): void
    {
        $this->config->save();
        
        $configFile = $this->testConfigDir . '/config/presets.json';
        $content = file_get_contents($configFile);
        $data = json_decode($content, true);
        
        $this->assertArrayHasKey('last_updated', $data);
    }
    
    public function test_loads_existing_config(): void
    {
        // Write a config file directly
        $configFile = $this->testConfigDir . '/config/presets.json';
        $data = [
            'repository' => 'https://github.com/custom/existing.git',
            'branch' => 'feature',
        ];
        file_put_contents($configFile, json_encode($data));
        
        // Create new instance to load existing config
        $newConfig = new PresetConfig($this->testConfigDir);
        
        $this->assertEquals('https://github.com/custom/existing.git', $newConfig->get('repository'));
        $this->assertEquals('feature', $newConfig->get('branch'));
    }
    
    public function test_env_override_for_branch(): void
    {
        putenv('LARAVEL_DEV_PRESETS_BRANCH=develop');
        
        $this->assertEquals('develop', $this->config->get('branch'));
        
        // Cleanup
        putenv('LARAVEL_DEV_PRESETS_BRANCH');
    }
}
