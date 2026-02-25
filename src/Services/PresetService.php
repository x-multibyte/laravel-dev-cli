<?php

namespace LaravelDev\Services;

use LaravelDev\Domain\Preset;
use LaravelDev\Support\Filesystem;

class PresetService
{
    public const DEFAULT_CACHE_PATH = '~/.laravel-dev/presets';
    
    private string $cachePath;
    private Filesystem $fs;
    
    public function __construct(?string $cachePath = null)
    {
        $this->fs = new Filesystem();
        $this->cachePath = $this->fs->expandHomePath($cachePath ?? self::DEFAULT_CACHE_PATH);
    }
    
    public function list(?string $category = null, ?string $laravel = null): array
    {
        $presets = [];
        
        if (!is_dir($this->cachePath)) {
            return $presets;
        }
        
        $categories = $category ? [$category] : $this->getCategories();
        
        foreach ($categories as $cat) {
            $catPath = $this->cachePath . '/' . $cat;
            if (!is_dir($catPath)) {
                continue;
            }
            
            foreach (glob($catPath . '/*.json') as $file) {
                $preset = $this->loadPreset($file);
                
                if ($preset) {
                    if ($laravel === null || $preset->getLaravelMajorVersion() === (int) $laravel) {
                        $presets[] = $preset;
                    }
                }
            }
        }
        
        return $presets;
    }
    
    public function get(string $name): ?Preset
    {
        $parts = explode('/', $name);
        if (count($parts) !== 2) {
            return null;
        }
        
        [$category, $version] = $parts;
        $file = $this->cachePath . '/' . $category . '/' . $version . '.json';
        
        if (!file_exists($file)) {
            return null;
        }
        
        return $this->loadPreset($file);
    }
    
    public function getCachePath(): string
    {
        return $this->cachePath;
    }
    
    private function getCategories(): array
    {
        $categories = [];
        
        foreach (glob($this->cachePath . '/*', GLOB_ONLYDIR) as $dir) {
            $categories[] = basename($dir);
        }
        
        return $categories;
    }
    
    private function loadPreset(string $file): ?Preset
    {
        $content = file_get_contents($file);
        if ($content === false) {
            return null;
        }
        
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        
        return Preset::fromArray($data);
    }
}