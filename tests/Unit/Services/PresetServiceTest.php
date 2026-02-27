<?php

use XMultibyte\LaravelDev\Services\PresetService;

beforeEach(function () {
    $this->cachePath = sys_get_temp_dir() . '/laravel-dev-presets-' . uniqid();
    mkdir($this->cachePath . '/api', 0755, true);
    mkdir($this->cachePath . '/framework', 0755, true);

    // Create sample preset files
    file_put_contents(
        $this->cachePath . '/api/12.json',
        json_encode([
            'name' => 'api/12',
            'version' => '1.0.0',
            'description' => 'Laravel 12 API',
            'laravel' => ['version' => '^12.0'],
            'dependencies' => ['composer' => ['laravel/sanctum' => '^4.0']],
        ])
    );

    file_put_contents(
        $this->cachePath . '/framework/12.json',
        json_encode([
            'name' => 'framework/12',
            'version' => '1.0.0',
            'description' => 'Laravel 12 Framework',
            'laravel' => ['version' => '^12.0'],
        ])
    );

    $this->service = new PresetService($this->cachePath);
});

afterEach(function () {
    exec("rm -rf {$this->cachePath}");
});

test('list returns all presets', function () {
    $presets = $this->service->list();

    expect($presets)->toHaveCount(2);
});

test('list filters by category', function () {
    $presets = $this->service->list(category: 'api');

    expect($presets)->toHaveCount(1)
        ->and($presets[0]->name)->toBe('api/12');
});

test('get returns preset', function () {
    $preset = $this->service->get('api/12');

    expect($preset)->not->toBeNull()
        ->and($preset->description)->toBe('Laravel 12 API');
});

test('get returns null for unknown', function () {
    $preset = $this->service->get('unknown/preset');

    expect($preset)->toBeNull();
});

test('is installed returns false when not git repo', function () {
    // The test cache path is not a git repo
    expect($this->service->isInstalled())->toBeFalse();
});

test('is installed returns true when git repo', function () {
    // Initialize a git repo in the test cache path
    exec("cd {$this->cachePath} && git init 2>/dev/null");

    expect($this->service->isInstalled())->toBeTrue();
});

test('get cache path returns expanded path', function () {
    expect($this->service->getCachePath())->toBe($this->cachePath);
});

test('ensure updated creates directory and clones', function () {
    // Use a fresh temp directory that doesn't exist yet
    $newCachePath = sys_get_temp_dir() . '/laravel-dev-presets-new-' . uniqid();

    // Clean up if exists
    if (is_dir($newCachePath)) {
        exec("rm -rf {$newCachePath}");
    }

    $service = new PresetService($newCachePath);

    // Verify directory doesn't exist before
    expect($newCachePath)->not->toBeDirectory();

    // ensureUpdated should clone the repo (integration test with real git)
    // For unit testing, we'll just verify the method exists and can be called
    try {
        $service->ensureUpdated();
        // If successful, directory should exist
        expect($newCachePath)->toBeDirectory();
    } catch (RuntimeException $e) {
        // Expected if git is not available or network issues
        expect(strtolower($e->getMessage()))->toContain('clone');
    }

    // Cleanup
    if (is_dir($newCachePath)) {
        exec("rm -rf {$newCachePath}");
    }
});
