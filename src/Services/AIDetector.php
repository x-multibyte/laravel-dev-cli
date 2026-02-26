<?php

namespace XMultibyte\LaravelDev\Services;

use XMultibyte\LaravelDev\Domain\AIPlatform;

class AIDetector
{
    public function detect(string $projectPath): array
    {
        $detected = [];
        
        foreach (AIPlatform::all() as $platform) {
            $configFolder = $platform->getConfigFolder();
            if ($configFolder && is_dir($projectPath . '/' . $configFolder)) {
                $detected[] = $platform;
            }
        }
        
        return $detected;
    }
    
    public function getSkillPath(AIPlatform $platform, string $projectPath): string
    {
        return sprintf(
            '%s/%s/%s/laravel-dev',
            $projectPath,
            $platform->getConfigFolder(),
            $platform->getSkillPath()
        );
    }
    
    public function getSupportedPlatforms(): array
    {
        return AIPlatform::all();
    }
    
    public function getSuggestedPlatform(array $detected): ?AIPlatform
    {
        if (count($detected) === 1) {
            return $detected[0];
        }
        
        if (count($detected) > 1) {
            return AIPlatform::ALL;
        }
        
        return null;
    }
}