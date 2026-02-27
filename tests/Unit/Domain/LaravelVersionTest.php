<?php

use XMultibyte\LaravelDev\Domain\LaravelVersion;

test('parse major version', function () {
    $version = LaravelVersion::parse('12');

    expect($version->major)->toBe(12)
        ->and($version->minor)->toBeNull();
});

test('parse full version', function () {
    $version = LaravelVersion::parse('12.1');

    expect($version->major)->toBe(12)
        ->and($version->minor)->toBe(1);
});

test('get docs path', function () {
    $version = new LaravelVersion(12);

    expect($version->getDocsPath())->toBe('v12');
});

test('is supported', function (string $version, bool $expected) {
    expect(LaravelVersion::isSupported($version))->toBe($expected);
})->with([
    ['10', true],
    ['11', true],
    ['12', true],
    ['9', false],
]);

test('get supported', function () {
    $supported = LaravelVersion::getSupported();

    expect($supported)->toBe(['10', '11', '12']);
});
