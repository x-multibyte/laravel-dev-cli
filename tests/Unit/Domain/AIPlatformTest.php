<?php

namespace Tests\Unit\Domain;

use LaravelDev\Domain\AIPlatform;
use PHPUnit\Framework\TestCase;

class AIPlatformTest extends TestCase
{
    public function test_claude_has_correct_config_folder(): void
    {
        $this->assertEquals('.claude', AIPlatform::CLAUDE->getConfigFolder());
    }
    
    public function test_claude_has_correct_skill_path(): void
    {
        $this->assertEquals('skills', AIPlatform::CLAUDE->getSkillPath());
    }
    
    public function test_all_platforms_have_display_name(): void
    {
        foreach (AIPlatform::cases() as $platform) {
            $this->assertNotEmpty($platform->getDisplayName());
        }
    }
    
    public function test_all_platform_returns_all_cases_except_itself(): void
    {
        $all = AIPlatform::all();
        
        $this->assertNotContains(AIPlatform::ALL, $all);
        $this->assertContains(AIPlatform::CLAUDE, $all);
    }
}