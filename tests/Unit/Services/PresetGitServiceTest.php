<?php

use XMultibyte\LaravelDev\Services\PresetGitService;

beforeEach(function () {
    $this->testDir = sys_get_temp_dir() . '/laravel-dev-git-test-' . uniqid();
    mkdir($this->testDir, 0755, true);
    $this->gitService = new PresetGitService;
});

afterEach(function () {
    gitTestRemoveDirectory($this->testDir);
});

function gitTestRemoveDirectory(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }

    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . '/' . $item;
        is_dir($path) ? gitTestRemoveDirectory($path) : unlink($path);
    }
    rmdir($dir);
}

test('is git repo returns false for non git dir', function () {
    expect($this->gitService->isGitRepo($this->testDir))->toBeFalse();
});

test('clone creates git repo', function () {
    // Use a small public repo for testing
    $repo = 'https://github.com/octocat/Hello-World.git';

    $this->gitService->clone($repo, $this->testDir);

    expect($this->gitService->isGitRepo($this->testDir))->toBeTrue();
    expect($this->testDir . '/README')->toBeFile();
})->skip('Network dependent test');

test('clone throws exception for invalid repo', function () {
    expect(fn () => $this->gitService->clone('https://invalid-url-not-exist.git', $this->testDir))
        ->toThrow(RuntimeException::class);
})->skip('Network dependent test');
