<?php

namespace XMultibyte\LaravelDev\Tests\Unit\Console;

use XMultibyte\LaravelDev\Console\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function test_application_has_correct_name(): void
    {
        $app = new Application();
        
        $this->assertEquals('laravel-dev', $app->getName());
    }
    
    public function test_application_has_version(): void
    {
        $app = new Application();
        
        $this->assertNotNull($app->getVersion());
    }
}