<?php

use XMultibyte\LaravelDev\Services\DocsService;
use XMultibyte\LaravelDev\Support\HttpClient;

beforeEach(function () {
    $this->docsPath = sys_get_temp_dir().'/laravel-dev-docs-test-'.uniqid();
    @mkdir($this->docsPath.'/v12', 0755, true);
});

afterEach(function () {
    // Clean up test directory
    if (is_dir($this->docsPath)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->docsPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $file->isDir() ? @rmdir($file->getRealPath()) : @unlink($file->getRealPath());
        }

        @rmdir($this->docsPath);
    }
});

test('search throws exception on empty queries', function () {
    $service = new DocsService($this->docsPath);

    $service->search([]);
})->throws(RuntimeException::class, 'At least one search query is required');

test('search throws exception on empty query strings', function () {
    $service = new DocsService($this->docsPath);

    $service->search(['', '  ']);
})->throws(RuntimeException::class, 'At least one search query is required');

test('detect packages from composer.json', function () {
    $testDir = sys_get_temp_dir().'/test-project-'.uniqid();
    mkdir($testDir, 0755, true);
    file_put_contents($testDir.'/composer.json', json_encode([
        'require' => [
            'laravel/framework' => '^11.0',
            'livewire/livewire' => '^3.0',
            'guzzlehttp/guzzle' => '^7.0',
        ],
        'require-dev' => [
            'pestphp/pest' => '^2.0',
        ],
    ]));

    $service = new DocsService($this->docsPath);
    $packages = $service->detectPackages($testDir);

    expect($packages)->toBeArray()
        ->toHaveCount(3)
        ->toContain(
            ['name' => 'laravel/framework', 'version' => '11.x'],
            ['name' => 'livewire/livewire', 'version' => '3.x'],
            ['name' => 'pestphp/pest', 'version' => '2.x']
        );

    unlink($testDir.'/composer.json');
    rmdir($testDir);
});

test('detect packages returns empty array when no composer.json', function () {
    $service = new DocsService($this->docsPath);
    $packages = $service->detectPackages('/nonexistent/path');

    expect($packages)->toBeArray()->toBeEmpty();
});

test('detect packages ignores unsupported packages', function () {
    $testDir = sys_get_temp_dir().'/test-project-unsupported-'.uniqid();
    mkdir($testDir, 0755, true);
    file_put_contents($testDir.'/composer.json', json_encode([
        'require' => [
            'symfony/console' => '^6.0',
            'guzzlehttp/guzzle' => '^7.0',
        ],
    ]));

    $service = new DocsService($this->docsPath);
    $packages = $service->detectPackages($testDir);

    expect($packages)->toBeArray()->toBeEmpty();

    unlink($testDir.'/composer.json');
    rmdir($testDir);
});

test('detect packages handles various version formats', function () {
    $testDir = sys_get_temp_dir().'/test-project-versions-'.uniqid();
    mkdir($testDir, 0755, true);
    file_put_contents($testDir.'/composer.json', json_encode([
        'require' => [
            'laravel/framework' => '^10.0',
            'livewire/livewire' => '~3.0',
            'filament/filament' => 'v3.0',
            'pestphp/pest' => '2.*',
        ],
    ]));

    $service = new DocsService($this->docsPath);
    $packages = $service->detectPackages($testDir);

    expect($packages)->toBeArray()
        ->toHaveCount(4)
        ->toContain(
            ['name' => 'laravel/framework', 'version' => '10.x'],
            ['name' => 'livewire/livewire', 'version' => '3.x'],
            ['name' => 'filament/filament', 'version' => '3.x'],
            ['name' => 'pestphp/pest', 'version' => '2.x']
        );

    unlink($testDir.'/composer.json');
    rmdir($testDir);
});

test('get fallback returns local documentation', function () {
    $content = '# Routing Documentation

This is the routing documentation content.';

    file_put_contents($this->docsPath.'/v12/routing.md', $content);

    $service = new DocsService($this->docsPath);
    $result = $service->getFallback('routing', '12');

    expect($result)->toBe($content);
});

test('get fallback returns null for unknown topic', function () {
    $service = new DocsService($this->docsPath);
    $result = $service->getFallback('unknown-topic', '12');

    expect($result)->toBeNull();
});

test('get fallback returns null when file not found', function () {
    $service = new DocsService($this->docsPath);
    $result = $service->getFallback('routing', '12');

    expect($result)->toBeNull();
});

test('get fallback maps eloquent to database', function () {
    $content = '# Database Documentation';

    file_put_contents($this->docsPath.'/v12/database.md', $content);

    $service = new DocsService($this->docsPath);
    $result = $service->getFallback('eloquent', '12');

    expect($result)->toBe($content);
});

test('get supported packages returns list', function () {
    $service = new DocsService($this->docsPath);
    $packages = $service->getSupportedPackages();

    expect($packages)->toBeArray()
        ->toContain('laravel/framework')
        ->toContain('livewire/livewire')
        ->toContain('filament/filament');
});

test('detect packages handles invalid json', function () {
    $testDir = sys_get_temp_dir().'/test-project-invalid-'.uniqid();
    mkdir($testDir, 0755, true);
    file_put_contents($testDir.'/composer.json', 'invalid json content {{{');

    $service = new DocsService($this->docsPath);
    $packages = $service->detectPackages($testDir);

    expect($packages)->toBeArray()->toBeEmpty();

    unlink($testDir.'/composer.json');
    rmdir($testDir);
});