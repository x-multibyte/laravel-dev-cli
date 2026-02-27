<?php

use XMultibyte\LaravelDev\Services\PresetConfig;

beforeEach(function () {
    $this->testConfigDir = sys_get_temp_dir() . '/laravel-dev-test-' . uniqid();
    mkdir($this->testConfigDir . '/config', 0755, true);
    $this->config = new PresetConfig($this->testConfigDir);
});

afterEach(function () {
    if (is_dir($this->testConfigDir)) {
        testRemoveDirectory($this->testConfigDir);
    }
});

function testRemoveDirectory(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }

    $items = scandir($dir);
    if ($items === false) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            testRemoveDirectory($path);
        } else {
            unlink($path);
        }
    }

    rmdir($dir);
}

test('get default repository', function () {
    expect($this->config->get('repository'))
        ->toBe('https://github.com/x-multibyte/laravel-dev-presets.git');
});

test('get default branch', function () {
    expect($this->config->get('branch'))->toBe('main');
});

test('get with env override', function () {
    putenv('LARAVEL_DEV_PRESETS_REPO=https://github.com/custom/presets.git');

    expect($this->config->get('repository'))
        ->toBe('https://github.com/custom/presets.git');

    // Cleanup
    putenv('LARAVEL_DEV_PRESETS_REPO');
});

test('set and save', function () {
    $this->config->set('branch', 'develop');
    $this->config->save();

    expect($this->config->get('branch'))->toBe('develop');

    // Verify file was created
    $configFile = $this->testConfigDir . '/config/presets.json';
    expect($configFile)->toBeFile();
});

test('all returns all config', function () {
    $all = $this->config->all();

    expect($all)
        ->toHaveKey('repository')
        ->toHaveKey('branch');
});

test('get non existing key returns default', function () {
    expect($this->config->get('non_existing_key'))->toBeNull()
        ->and($this->config->get('non_existing_key', 'default_value'))->toBe('default_value');
});

test('reset clears loaded config', function () {
    $this->config->set('branch', 'develop');
    expect($this->config->get('branch'))->toBe('develop');

    $this->config->reset();

    // After reset, should return default value since config wasn't saved
    expect($this->config->get('branch'))->toBe('main');
});

test('save includes last updated', function () {
    $this->config->save();

    $configFile = $this->testConfigDir . '/config/presets.json';
    $content = file_get_contents($configFile);
    $data = json_decode($content, true);

    expect($data)->toHaveKey('last_updated');
});

test('loads existing config', function () {
    // Write a config file directly
    $configFile = $this->testConfigDir . '/config/presets.json';
    $data = [
        'repository' => 'https://github.com/custom/existing.git',
        'branch' => 'feature',
    ];
    file_put_contents($configFile, json_encode($data));

    // Create new instance to load existing config
    $newConfig = new PresetConfig($this->testConfigDir);

    expect($newConfig->get('repository'))->toBe('https://github.com/custom/existing.git')
        ->and($newConfig->get('branch'))->toBe('feature');
});

test('env override for branch', function () {
    putenv('LARAVEL_DEV_PRESETS_BRANCH=develop');

    expect($this->config->get('branch'))->toBe('develop');

    // Cleanup
    putenv('LARAVEL_DEV_PRESETS_BRANCH');
});
