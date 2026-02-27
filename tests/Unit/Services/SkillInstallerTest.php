<?php

use XMultibyte\LaravelDev\Domain\AIPlatform;
use XMultibyte\LaravelDev\Services\SkillInstaller;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir() . '/laravel-dev-skill-test-' . uniqid();
    $this->globalPath = $this->tempDir . '/global';
    $this->installer = new SkillInstaller($this->globalPath);

    mkdir($this->tempDir . '/project', 0755, true);
});

afterEach(function () {
    exec("rm -rf {$this->tempDir}");
});

test('ensure global presets creates directory', function () {
    $this->installer->ensureGlobalPresets();

    expect($this->globalPath . '/presets')->toBeDirectory();
});

test('get skill target path', function () {
    $path = $this->installer->getSkillTargetPath(
        AIPlatform::CLAUDE,
        $this->tempDir . '/project'
    );

    expect($path)->toBe($this->tempDir . '/project/.claude/skills/laravel-dev');
});

test('get global presets path', function () {
    $path = $this->installer->getGlobalPresetsPath();

    expect($path)->toBe($this->globalPath . '/presets');
});
