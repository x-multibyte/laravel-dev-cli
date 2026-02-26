<?php

namespace XMultibyte\LaravelDev\Tests\Unit\Support;

use XMultibyte\LaravelDev\Support\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    private Filesystem $fs;
    private string $tempDir;
    
    protected function setUp(): void
    {
        $this->fs = new Filesystem();
        $this->tempDir = sys_get_temp_dir() . '/laravel-dev-test-' . uniqid();
        mkdir($this->tempDir, 0755, true);
    }
    
    protected function tearDown(): void
    {
        $this->fs->deleteDirectory($this->tempDir);
    }
    
    public function test_ensure_directory_exists(): void
    {
        $path = $this->tempDir . '/new/dir';
        
        $this->fs->ensureDirectoryExists($path);
        
        $this->assertDirectoryExists($path);
    }
    
    public function test_expand_home_path(): void
    {
        $result = $this->fs->expandHomePath('~/test');
        
        $this->assertEquals($_SERVER['HOME'] . '/test', $result);
    }
    
    public function test_write_file(): void
    {
        $path = $this->tempDir . '/test.txt';
        
        $this->fs->write($path, 'Hello World');
        
        $this->assertFileExists($path);
        $this->assertEquals('Hello World', file_get_contents($path));
    }
    
    public function test_delete_directory(): void
    {
        $path = $this->tempDir . '/to-delete';
        mkdir($path . '/nested', 0755, true);
        file_put_contents($path . '/file.txt', 'test');
        
        $this->fs->deleteDirectory($path);
        
        $this->assertDirectoryDoesNotExist($path);
    }
}