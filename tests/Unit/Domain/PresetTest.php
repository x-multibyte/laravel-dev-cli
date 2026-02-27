<?php

use XMultibyte\LaravelDev\Domain\Preset;

beforeEach(function () {
    $this->sampleData = [
        'name' => 'api/12',
        'version' => '1.0.0',
        'description' => 'Laravel 12 API project',
        'laravel' => '^12.0',
        'dependencies' => [
            'composer' => ['laravel/sanctum' => '^4.0'],
            'npm' => [],
        ],
        'dev_dependencies' => [
            'composer' => [],
            'npm' => [],
        ],
        'env_template' => [
            'APP_NAME' => 'Laravel',
        ],
        'commands' => ['php artisan key:generate'],
        'hooks' => [
            'after_install' => ['php artisan migrate'],
        ],
        'metadata' => [
            'tags' => ['api', 'sanctum'],
            'category' => 'api',
        ],
    ];
});

test('from array creates preset', function () {
    $preset = Preset::fromArray($this->sampleData);

    expect($preset->name)->toBe('api/12')
        ->and($preset->version)->toBe('1.0.0')
        ->and($preset->description)->toBe('Laravel 12 API project');
});

test('get category', function () {
    $preset = Preset::fromArray($this->sampleData);

    expect($preset->getCategory())->toBe('api');
});

test('get laravel major version', function () {
    $preset = Preset::fromArray($this->sampleData);

    expect($preset->getLaravelMajorVersion())->toBe(12);
});
