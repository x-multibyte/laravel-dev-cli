<?php

use XMultibyte\LaravelDev\Support\HttpClient;

test('get returns response body', function () {
    $client = new HttpClient;

    // Using a stable public API for testing
    $response = $client->get('https://httpbin.org/json');

    expect($response)
        ->toBeString()
        ->toBeJson();
});

test('get json returns array', function () {
    $client = new HttpClient;

    $response = $client->getJson('https://httpbin.org/json');

    expect($response)->toBeArray();
});

test('post returns response body', function () {
    $client = new HttpClient;

    $response = $client->post('https://httpbin.org/post', [
        'query' => 'test',
        'packages' => [['name' => 'laravel/framework']],
    ]);

    expect($response)
        ->toBeString()
        ->toBeJson();

    $decoded = json_decode($response, true);
    expect($decoded['json']['query'])->toBe('test');
    expect($decoded['json']['packages'])->toBe([['name' => 'laravel/framework']]);
});

test('post sends json content type', function () {
    $client = new HttpClient;

    $response = $client->post('https://httpbin.org/post', ['test' => 'data']);

    $decoded = json_decode($response, true);
    expect($decoded['headers']['Content-Type'])->toContain('application/json');
});

test('post throws exception on invalid url', function () {
    $client = new HttpClient;

    $client->post('https://invalid.example.com/api', ['test' => 'data']);
})->throws(RuntimeException::class);

test('get throws exception on invalid url', function () {
    $client = new HttpClient;

    $client->get('https://invalid.example.com/api');
})->throws(RuntimeException::class);
