<?php

namespace XMultibyte\LaravelDev\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpClient
{
    private Client $client;
    
    public function __construct(array $config = [])
    {
        $this->client = new Client(array_merge([
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'laravel-dev-cli/1.0.3',
            ],
        ], $config));
    }
    
    public function get(string $url, array $options = []): string
    {
        try {
            $response = $this->client->get($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new \RuntimeException("HTTP GET failed: {$url} - " . $e->getMessage(), 0, $e);
        }
    }
    
    public function getJson(string $url, array $options = []): array
    {
        $body = $this->get($url, $options);
        $decoded = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                "Failed to decode JSON response from {$url}: " . json_last_error_msg(),
                0
            );
        }
        
        return $decoded;
    }
    
    public function download(string $url, string $destination): void
    {
        try {
            $this->client->get($url, [
                'sink' => $destination,
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Download failed: {$url} - " . $e->getMessage(), 0, $e);
        }
    }
}