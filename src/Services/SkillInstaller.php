<?php

namespace LaravelDev\Services;

use LaravelDev\Domain\AIPlatform;
use LaravelDev\Support\Filesystem;

class SkillInstaller
{
    public const SKILL_REPO = 'x-multibyte/laravel-dev-skill';
    public const PRESETS_REPO = 'x-multibyte/laravel-dev-presets';
    
    private string $globalPath;
    private Filesystem $fs;
    
    public function __construct(?string $globalPath = null)
    {
        $this->fs = new Filesystem();
        $this->globalPath = $this->fs->expandHomePath(
            $globalPath ?? '~/.laravel-dev'
        );
    }
    
    public function ensureGlobalPresets(): void
    {
        $presetsPath = $this->getGlobalPresetsPath();
        $this->fs->ensureDirectoryExists($presetsPath);
    }
    
    public function getSkillTargetPath(AIPlatform $platform, string $projectPath): string
    {
        return sprintf(
            '%s/%s/%s/laravel-dev',
            $projectPath,
            $platform->getConfigFolder(),
            $platform->getSkillPath()
        );
    }
    
    public function getGlobalPresetsPath(): string
    {
        return $this->globalPath . '/presets';
    }
    
    public function getGlobalSkillPath(): string
    {
        return $this->globalPath . '/skill';
    }
    
    public function install(AIPlatform $platform, string $projectPath, bool $force = false): array
    {
        // Ensure global directories exist
        $this->ensureGlobalPresets();
        
        // Get target path
        $targetPath = $this->getSkillTargetPath($platform, $projectPath);
        
        // Check if already exists
        if ($this->fs->exists($targetPath) && !$force) {
            throw new \RuntimeException("SKILL already installed at: {$targetPath}. Use --force to overwrite.");
        }
        
        // Create target directory
        $this->fs->ensureDirectoryExists($targetPath);
        
        // Return installed path
        return [$targetPath];
    }
}