<?php

namespace XMultibyte\LaravelDev\Services;

use RuntimeException;
use XMultibyte\LaravelDev\Support\Filesystem;
use XMultibyte\LaravelDev\Support\HttpClient;

class DocsService
{
    private const BOOST_API_URL = 'https://boost.laravel.com/api/docs';

    private const DEFAULT_TOKEN_LIMIT = 5000;

    private const MAX_TOKEN_LIMIT = 100000;

    private const SUPPORTED_PACKAGES = [
        'laravel/framework',
        'livewire/livewire',
        'laravel/nova',
        'filament/filament',
        'inertiajs/inertia-laravel',
        'pestphp/pest',
    ];

    private HttpClient $http;

    private Filesystem $fs;

    private string $docsPath;

    public function __construct(?string $docsPath = null)
    {
        $this->http = new HttpClient;
        $this->fs = new Filesystem;
        $this->docsPath = $this->fs->expandHomePath(
            $docsPath ?? '~/.laravel-dev/skill/references'
        );
    }

    /**
     * Search documentation using Laravel Boost API.
     *
     * @param  array<int, string>  $queries
     * @param  array<int, array{name: string, version: string}>  $packages
     */
    public function search(
        array $queries,
        array $packages = [],
        int $tokenLimit = self::DEFAULT_TOKEN_LIMIT
    ): string {
        $queries = array_filter(array_map('trim', $queries), fn ($q) => $q !== '');

        if (empty($queries)) {
            throw new RuntimeException('At least one search query is required');
        }

        $tokenLimit = min(max($tokenLimit, 500), self::MAX_TOKEN_LIMIT);

        if (empty($packages)) {
            $packages = [['name' => 'laravel/framework', 'version' => '12.x']];
        }

        $payload = [
            'queries' => $queries,
            'packages' => $packages,
            'token_limit' => $tokenLimit,
            'format' => 'markdown',
        ];

        try {
            return $this->http->post(self::BOOST_API_URL, $payload);
        } catch (RuntimeException $e) {
            throw new RuntimeException(
                'Failed to search documentation: '.$e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Detect packages from composer.json in the given directory.
     *
     * @return array<int, array{name: string, version: string}>
     */
    public function detectPackages(string $directory): array
    {
        $composerPath = rtrim($directory, '/').'/composer.json';

        if (! is_file($composerPath) || ! is_readable($composerPath)) {
            return [];
        }

        $content = file_get_contents($composerPath);

        if ($content === false) {
            return [];
        }

        $composer = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        $packages = [];
        $dependencies = array_merge(
            $composer['require'] ?? [],
            $composer['require-dev'] ?? []
        );

        foreach ($dependencies as $name => $version) {
            if (! in_array($name, self::SUPPORTED_PACKAGES, true)) {
                continue;
            }

            $majorVersion = $this->extractMajorVersion($version);

            if ($majorVersion === null) {
                continue;
            }

            $packages[] = [
                'name' => $name,
                'version' => $majorVersion.'.x',
            ];
        }

        return $packages;
    }

    /**
     * Extract major version from composer version constraint.
     */
    private function extractMajorVersion(string $version): ?int
    {
        // Handle versions like "^10.0", "~11.0", "12.*", "v10.0", "10.x-dev"
        $version = ltrim($version, '^~v');
        $version = str_replace('.*', '', $version);
        $version = str_replace('-dev', '', $version);

        if (preg_match('/^(\d+)/', $version, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Fallback to local documentation files.
     */
    public function getFallback(string $topic, string $version = '12'): ?string
    {
        $topicMap = [
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

        $filename = $topicMap[strtolower($topic)] ?? null;

        if (! $filename) {
            return null;
        }

        $filePath = $this->docsPath.'/v'.$version.'/'.$filename;

        if (! is_file($filePath) || ! is_readable($filePath)) {
            return null;
        }

        $content = @file_get_contents($filePath);

        if ($content === false) {
            return null;
        }

        return $content;
    }

    /**
     * Get list of supported packages.
     *
     * @return array<int, string>
     */
    public function getSupportedPackages(): array
    {
        return self::SUPPORTED_PACKAGES;
    }
}