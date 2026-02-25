<?php

namespace Tests\Unit\Domain;

use LaravelDev\Domain\LaravelVersion;
use PHPUnit\Framework\TestCase;

class LaravelVersionTest extends TestCase
{
    public function test_parse_major_version(): void
    {
        $version = LaravelVersion::parse('12');
        
        $this->assertEquals(12, $version->major);
        $this->assertNull($version->minor);
    }
    
    public function test_parse_full_version(): void
    {
        $version = LaravelVersion::parse('12.1');
        
        $this->assertEquals(12, $version->major);
        $this->assertEquals(1, $version->minor);
    }
    
    public function test_get_docs_path(): void
    {
        $version = new LaravelVersion(12);
        
        $this->assertEquals('v12', $version->getDocsPath());
    }
    
    public function test_is_supported(): void
    {
        $this->assertTrue(LaravelVersion::isSupported('10'));
        $this->assertTrue(LaravelVersion::isSupported('11'));
        $this->assertTrue(LaravelVersion::isSupported('12'));
        $this->assertFalse(LaravelVersion::isSupported('9'));
    }
    
    public function test_get_supported(): void
    {
        $supported = LaravelVersion::getSupported();
        
        $this->assertEquals(['10', '11', '12'], $supported);
    }
}