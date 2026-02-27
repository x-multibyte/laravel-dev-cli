<?php

namespace XMultibyte\LaravelDev\Services;

use XMultibyte\LaravelDev\Domain\AIPlatform;
use XMultibyte\LaravelDev\Support\Filesystem;
use XMultibyte\LaravelDev\Support\HttpClient;

class SkillInstaller
{
    public const SKILL_REPO = 'x-multibyte/laravel-dev-skill';
    public const PRESETS_REPO = 'x-multibyte/laravel-dev-presets';
    
    private string $globalPath;
    private Filesystem $fs;
    private HttpClient $httpClient;
    
    public function __construct(?string $globalPath = null, ?HttpClient $httpClient = null)
    {
        $this->fs = new Filesystem();
        $this->globalPath = $this->fs->expandHomePath(
            $globalPath ?? '~/.laravel-dev'
        );
        $this->httpClient = $httpClient ?? new HttpClient();
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
    
    private function downloadSkillFiles(): string
    {
        $skillPath = $this->getGlobalSkillPath();
        $tempDir = sys_get_temp_dir() . '/laravel-dev-skill-' . uniqid();
        
        try {
            // Create temporary directory for download
            $this->fs->ensureDirectoryExists($tempDir);
            
            // Download the repository as a ZIP archive
            $zipUrl = "https://github.com/" . self::SKILL_REPO . "/archive/refs/heads/main.zip";
            $zipPath = $tempDir . '/skill.zip';
            
            $this->httpClient->download($zipUrl, $zipPath);
            
            // Extract the ZIP file using unzip command to preserve symbolic links
            $extractPath = $tempDir . '/extracted';
            $this->fs->ensureDirectoryExists($extractPath);
            
            $unzipCommand = "unzip -q -o " . escapeshellarg($zipPath) . " -d " . escapeshellarg($extractPath);
            $output = [];
            $returnCode = 0;
            exec($unzipCommand, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \RuntimeException("Failed to extract ZIP file: {$unzipCommand}");
            }
            
            // Find the extracted directory (it will have the repo name)
            $extractedDir = null;
            $items = array_diff(scandir($extractPath), ['.', '..']);
            if (!empty($items)) {
                $firstItem = reset($items);
                $extractedDir = $extractPath . '/' . $firstItem;
                
                if (is_dir($extractedDir)) {
                    // Copy the extracted files to the global skill path
                    $this->fs->ensureDirectoryExists($skillPath);
                    $this->fs->copyDirectory($extractedDir, $skillPath);
                    
                    // Set executable permissions for script files
                    $this->setScriptPermissions($skillPath);
                    
                    // Clean up temporary directory
                    $this->fs->deleteDirectory($tempDir);
                    
                    return $skillPath;
                } else {
                    throw new \RuntimeException("Extracted item is not a directory: {$extractedDir}");
                }
            } else {
                throw new \RuntimeException("No items found in extracted directory: {$extractPath}");
            }
        } catch (\Exception $e) {
            // Clean up on error
            $this->fs->deleteDirectory($tempDir);
            throw $e;
        }
    }
    
    // Public method for testing purposes
    public function testDownloadSkillFiles(): string
    {
        return $this->downloadSkillFiles();
    }
    
    private function setScriptPermissions(string $skillPath): void
    {
        $scriptsPath = $skillPath . '/scripts';
        if (!is_dir($scriptsPath)) {
            return;
        }
        
        // Set executable permissions for shell scripts
        foreach (glob($scriptsPath . '/*.sh') as $scriptFile) {
            chmod($scriptFile, 0755);
        }
        
        // Set executable permissions for Python scripts
        foreach (glob($scriptsPath . '/*.py') as $scriptFile) {
            chmod($scriptFile, 0755);
        }
    }
    
    public function install(AIPlatform $platform, string $projectPath, bool $force = false, bool $offline = false): array
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
        
        // If not in offline mode, download and copy skill files
        if (!$offline) {
            // Try to get skill files from global cache, download if not available
            $globalSkillPath = $this->getGlobalSkillPath();
            $files = glob($globalSkillPath . '/*');
            if (!$this->fs->exists($globalSkillPath) || $files === false || count($files) === 0) {
                $this->downloadSkillFiles();
            }
            
            // Copy skill files from global cache to target location
            if ($this->fs->exists($globalSkillPath)) {
                $scanned = scandir($globalSkillPath);
                if ($scanned === false) {
                    throw new \RuntimeException("Failed to scan directory: {$globalSkillPath}");
                }
                $items = array_diff($scanned, ['.', '..']);
                foreach ($items as $item) {
                    $source = $globalSkillPath . '/' . $item;
                    $destination = $targetPath . '/' . $item;
                    
                    if (is_dir($source)) {
                        $this->fs->copyDirectory($source, $destination);
                    } else {
                        $this->fs->copyFile($source, $destination);
                    }
                }
            }
        }
        
        // Return installed path
        return [$targetPath];
    }
}