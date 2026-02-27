<?php

use XMultibyte\LaravelDev\Domain\AIPlatform;
use XMultibyte\LaravelDev\Services\AIDetector;

beforeEach(function () {
    $this->detector = new AIDetector;
    $this->tempDir = sys_get_temp_dir() . '/laravel-dev-detector-' . uniqid();
    mkdir($this->tempDir, 0755, true);
});

afterEach(function () {
    exec("rm -rf {$this->tempDir}");
});

test('detect returns empty array when no platforms', function () {
    $detected = $this->detector->detect($this->tempDir);

    expect($detected)->toBeEmpty();
});

test('detect finds claude', function () {
    mkdir($this->tempDir . '/.claude', 0755, true);

    $detected = $this->detector->detect($this->tempDir);

    expect($detected)->toHaveCount(1)
        ->and($detected[0])->toBe(AIPlatform::CLAUDE);
});

test('detect finds multiple platforms', function () {
    mkdir($this->tempDir . '/.claude', 0755, true);
    mkdir($this->tempDir . '/.cursor', 0755, true);

    $detected = $this->detector->detect($this->tempDir);

    expect($detected)->toHaveCount(2)
        ->and($detected)->toContain(AIPlatform::CLAUDE)
        ->and($detected)->toContain(AIPlatform::CURSOR);
});

test('get skill path returns correct path', function () {
    $path = $this->detector->getSkillPath(AIPlatform::CLAUDE, $this->tempDir);

    expect($path)->toBe($this->tempDir . '/.claude/skills/laravel-dev');
});

test('get supported platforms returns all', function () {
    $platforms = $this->detector->getSupportedPlatforms();

    expect($platforms)
        ->not->toBeEmpty()
        ->not->toContain(AIPlatform::ALL);
});
