<?php

namespace LaravelDev\Services;

use LaravelDev\Support\Filesystem;

class DocsService
{
    private const VERSIONS = ['10', '11', '12'];
    
    private const TOPIC_MAP = [
        'routing' => 'routing.md',
        'database' => 'database.md',
        'eloquent' => 'database.md',
        'auth' => 'auth.md',
        'authentication' => 'auth.md',
        'cache' => 'cache.md',
        'queues' => 'queues.md',
        'testing' => 'testing.md',
        'artisan' => 'artisan.md',
        'configuration' => 'configuration.md',
        'structure' => 'structure.md',
        'views' => 'views.md',
        'blade' => 'views.md',
        'mail' => 'mail.md',
        'notifications' => 'notifications.md',
        'events' => 'events.md',
        'security' => 'security.md',
        'errors' => 'errors.md',
        'logging' => 'logging.md',
        'helpers' => 'helpers.md',
    ];
    
    private Filesystem $fs;
    private string $docsPath;
    
    public function __construct(?string $docsPath = null)
    {
        $this->fs = new Filesystem();
        $this->docsPath = $this->fs->expandHomePath(
            $docsPath ?? '~/.laravel-dev/skill/references'
        );
    }
    
    public function get(string $topic, string $version = '12'): ?string
    {
        $filename = self::TOPIC_MAP[strtolower($topic)] ?? null;
        
        if (!$filename) {
            return null;
        }
        
        $filePath = $this->docsPath . '/v' . $version . '/' . $filename;
        
        if (!is_file($filePath) || !is_readable($filePath)) {
            return null;
        }
        
        $content = @file_get_contents($filePath);
        
        if ($content === false) {
            return null;
        }
        
        return $content;
    }
    
    public function listTopics(): array
    {
        return array_keys(self::TOPIC_MAP);
    }
    
    public function getVersions(): array
    {
        return self::VERSIONS;
    }
}