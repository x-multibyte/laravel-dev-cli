<?php

use XMultibyte\LaravelDev\Domain\AIPlatform;

test('claude has correct config folder', function () {
    expect(AIPlatform::CLAUDE->getConfigFolder())->toBe('.claude');
});

test('claude has correct skill path', function () {
    expect(AIPlatform::CLAUDE->getSkillPath())->toBe('skills');
});

test('all platforms have display name', function () {
    foreach (AIPlatform::cases() as $platform) {
        expect($platform->getDisplayName())->not->toBeEmpty();
    }
});

test('all platforms returns all cases except itself', function () {
    $all = AIPlatform::all();

    expect($all)
        ->not->toContain(AIPlatform::ALL)
        ->toContain(AIPlatform::CLAUDE);
});
