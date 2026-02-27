<?php

namespace XMultibyte\LaravelDev\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use XMultibyte\LaravelDev\Services\PresetGitService;

class PresetGitServiceTest extends TestCase
{
    private string $testDir;
    private PresetGitService $gitService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = sys_get_temp_dir() . '/laravel-dev-git-test-' . uniqid();
        mkdir($this->testDir, 0755, true);
        $this->gitService = new PresetGitService();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->removeDirectory($this->testDir);
    }
    
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
    
    public function test_is_git_repo_returns_false_for_non_git_dir(): void
    {
        $this->assertFalse($this->gitService->isGitRepo($this->testDir));
    }
    
    public function test_clone_creates_git_repo(): void
    {
        // Use a small public repo for testing
        $repo = 'https://github.com/octocat/Hello-World.git';
        
        $this->gitService->clone($repo, $this->testDir);
        
        $this->assertTrue($this->gitService->isGitRepo($this->testDir));
        $this->assertFileExists($this->testDir . '/README');
    }
    
    public function test_clone_throws_exception_for_invalid_repo(): void
    {
        $this->expectException(\RuntimeException::class);
        
        $this->gitService->clone('https://invalid-url-not-exist.git', $this->testDir);
    }
}
