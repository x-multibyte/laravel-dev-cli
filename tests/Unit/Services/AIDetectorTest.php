<?php

namespace XMultibyte\LaravelDev\Tests\Unit\Services;

use XMultibyte\LaravelDev\Domain\AIPlatform;
use XMultibyte\LaravelDev\Services\AIDetector;
use PHPUnit\Framework\TestCase;

class AIDetectorTest extends TestCase
{
    private AIDetector $detector;
    private string $tempDir;
    
    protected function setUp(): void
    {
        $this->detector = new AIDetector();
        $this->tempDir = sys_get_temp_dir() . '/laravel-dev-detector-' . uniqid();
        mkdir($this->tempDir, 0755, true);
    }
    
    protected function tearDown(): void
    {
        exec("rm -rf {$this->tempDir}");
    }
    
    public function test_detect_returns_empty_array_when_no_platforms(): void
    {
        $detected = $this->detector->detect($this->tempDir);
        
        $this->assertEmpty($detected);
    }
    
    public function test_detect_finds_claude(): void
    {
        mkdir($this->tempDir . '/.claude', 0755, true);
        
        $detected = $this->detector->detect($this->tempDir);
        
        $this->assertCount(1, $detected);
        $this->assertEquals(AIPlatform::CLAUDE, $detected[0]);
    }
    
    public function test_detect_finds_multiple_platforms(): void
    {
        mkdir($this->tempDir . '/.claude', 0755, true);
        mkdir($this->tempDir . '/.cursor', 0755, true);
        
        $detected = $this->detector->detect($this->tempDir);
        
        $this->assertCount(2, $detected);
        $this->assertContains(AIPlatform::CLAUDE, $detected);
        $this->assertContains(AIPlatform::CURSOR, $detected);
    }
    
    public function test_get_skill_path_returns_correct_path(): void
    {
        $path = $this->detector->getSkillPath(AIPlatform::CLAUDE, $this->tempDir);
        
        $this->assertEquals($this->tempDir . '/.claude/skills/laravel-dev', $path);
    }
    
    public function test_get_supported_platforms_returns_all(): void
    {
        $platforms = $this->detector->getSupportedPlatforms();
        
        $this->assertNotEmpty($platforms);
        $this->assertNotContains(AIPlatform::ALL, $platforms);
    }
}