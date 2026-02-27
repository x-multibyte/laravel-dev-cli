<?php

use XMultibyte\LaravelDev\Support\Filesystem;

beforeEach(function () {
    $this->fs = new Filesystem;
    $this->tempDir = sys_get_temp_dir() . '/laravel-dev-test-' . uniqid();
    mkdir($this->tempDir, 0755, true);
});

afterEach(function () {
    $this->fs->deleteDirectory($this->tempDir);
});

test('ensure directory exists', function () {
    $path = $this->tempDir . '/new/dir';

    $this->fs->ensureDirectoryExists($path);

    expect($path)->toBeDirectory();
});

test('expand home path', function () {
    $result = $this->fs->expandHomePath('~/test');

    expect($result)->toBe($_SERVER['HOME'] . '/test');
});

test('write file', function () {
    $path = $this->tempDir . '/test.txt';

    $this->fs->write($path, 'Hello World');

    expect($path)->toBeFile()
        ->and(file_get_contents($path))->toBe('Hello World');
});

test('delete directory', function () {
    $path = $this->tempDir . '/to-delete';
    mkdir($path . '/nested', 0755, true);
    file_put_contents($path . '/file.txt', 'test');

    $this->fs->deleteDirectory($path);

    expect($path)->not->toBeDirectory();
});
