<?php

use XMultibyte\LaravelDev\Console\Application;

test('application has correct name', function () {
    $app = new Application;

    expect($app->getName())->toBe('laravel-dev');
});

test('application has version', function () {
    $app = new Application;

    expect($app->getVersion())->not->toBeNull();
});
