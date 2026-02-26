<?php

namespace XMultibyte\LaravelDev\Tests\Unit\Services;

use XMultibyte\LaravelDev\Domain\AIPlatform;
use XMultibyte\LaravelDev\Services\SkillInstaller;
use PHPUnit\Framework\TestCase;

class SkillInstallerTest extends TestCase
{
    private SkillInstaller $installer;
    private string $tempDir;
    private string $globalPath;
    
    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/laravel-dev-skill-test-' . uniqid();
        $this->globalPath = $this->tempDir . '/global';
        $this->installer = new SkillInstaller($this->globalPath);
        
        mkdir($this->tempDir . '/project', 0755, true);
    }
    
    protected function tearDown(): void
    {
        exec("rm -rf {$this->tempDir}");
    }
    
    public function test_ensure_global_presets_creates_directory(): void
    {
        $this->installer->ensureGlobalPresets();
        
        $this->assertDirectoryExists($this->globalPath . '/presets');
    }
    
    public function test_get_skill_target_path(): void
    {
        $path = $this->installer->getSkillTargetPath(
            AIPlatform::CLAUDE,
            $this->tempDir . '/project'
        );
        
        $this->assertEquals(
            $this->tempDir . '/project/.claude/skills/laravel-dev',
            $path
        );
    }
    
    public function test_get_global_presets_path(): void
    {
        $path = $this->installer->getGlobalPresetsPath();
        
        $this->assertEquals($this->globalPath . '/presets', $path);
    }
}