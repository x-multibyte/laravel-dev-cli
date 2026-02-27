<?php

namespace XMultibyte\LaravelDev\Support;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem
{
    private SymfonyFilesystem $fs;

    public function __construct()
    {
        $this->fs = new SymfonyFilesystem;
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
        $this->copyDirectoryRecursive($source, $destination);
    }

    private function copyDirectoryRecursive(string $source, string $destination): void
    {
        if (!is_dir($source)) {
            throw new \RuntimeException("Source is not a directory: {$source}");
        }

        $this->ensureDirectoryExists($destination);

        $items = scandir($source);
        if ($items === false) {
            throw new \RuntimeException("Failed to scan directory: {$source}");
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $sourcePath = $source . '/' . $item;
            $destPath = $destination . '/' . $item;

            if (is_link($sourcePath)) {
                // Preserve symbolic links
                $target = readlink($sourcePath);
                if ($target === false) {
                    throw new \RuntimeException("Failed to read link: {$sourcePath}");
                }
                // Remove existing file or link if it exists
                if ($this->fs->exists($destPath)) {
                    $this->fs->remove($destPath);
                }
                if (!symlink($target, $destPath)) {
                    throw new \RuntimeException("Failed to create symlink: {$destPath}");
                }
            } elseif (is_dir($sourcePath)) {
                // Recursively copy directories
                $this->copyDirectoryRecursive($sourcePath, $destPath);
            } else {
                // Copy files preserving permissions
                $this->copyFile($sourcePath, $destPath);
            }
        }
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
