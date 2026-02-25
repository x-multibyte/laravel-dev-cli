<?php

namespace Tests\Unit\Support;

use LaravelDev\Support\HttpClient;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    public function test_get_returns_response_body(): void
    {
        $client = new HttpClient();
        
        // Using a stable public API for testing
        $response = $client->get('https://httpbin.org/json');
        
        $this->assertIsString($response);
        $this->assertJson($response);
    }
    
    public function test_get_json_returns_array(): void
    {
        $client = new HttpClient();
        
        $response = $client->getJson('https://httpbin.org/json');
        
        $this->assertIsArray($response);
    }
}