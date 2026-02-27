<?php

namespace XMultibyte\LaravelDev\Support;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem
{
    private SymfonyFilesystem $fs;
    
    public function __construct()
    {
        $this->fs = new SymfonyFilesystem();
    }
    
    public function ensureDirectoryExists(string $path): void
    {
        if (!$this->fs->exists($path)) {
            $this->fs->mkdir($path, 0755);
        }
    }
    
    public function expandHomePath(string $path): string
    {
        if (str_starts_with($path, '~/')) {
            $home = $_SERVER['HOME'] ?? getenv('HOME');
            
            if ($home === null || $home === false || $home === '') {
                throw new \RuntimeException('HOME environment variable is not set. Cannot expand home path.');
            }
            
            return $home . substr($path, 1);
        }
        
        return $path;
    }
    
    public function write(string $path, string $content): void
    {
        $this->fs->dumpFile($path, $content);
    }
    
    public function read(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException("Failed to read file: {$path}");
        }
        return $content;
    }
    
    public function exists(string $path): bool
    {
        return $this->fs->exists($path);
    }
    
    public function deleteDirectory(string $path): void
    {
        if ($this->fs->exists($path)) {
            $this->fs->remove($path);
        }
    }
    
    public function copyDirectory(string $source, string $destination): void
    {
        $this->fs->mirror($source, $destination, null, [
            'override' => true,
            'copy_on_windows' => true,
            'delete' => false,
        ]);
    }
    
    public function copyFile(string $source, string $destination): void
    {
        if (!copy($source, $destination)) {
            throw new \RuntimeException("Failed to copy file from {$source} to {$destination}");
        }
        
        // Preserve file permissions
        $perms = fileperms($source);
        if ($perms !== false) {
            chmod($destination, $perms);
        }
    }
}