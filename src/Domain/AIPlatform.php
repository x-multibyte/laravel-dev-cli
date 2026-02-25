<?php

namespace LaravelDev\Domain;

enum AIPlatform: string
{
    case CLAUDE = 'claude';
    case CURSOR = 'cursor';
    case WINDSURF = 'windsurf';
    case ANTIGRAVITY = 'antigravity';
    case COPILOT = 'copilot';
    case KIRO = 'kiro';
    case CODEX = 'codex';
    case ROOCODE = 'roocode';
    case QODER = 'qoder';
    case GEMINI = 'gemini';
    case TRAE = 'trae';
    case OPENCODE = 'opencode';
    case CONTINUE = 'continue';
    case CODEBUDDY = 'codebuddy';
    case DROID = 'droid';
    case IFLOW = 'iflow';
    case ALL = 'all';
    
    public function getConfigFolder(): string
    {
        return match ($this) {
            self::CLAUDE => '.claude',
            self::CURSOR => '.cursor',
            self::WINDSURF => '.windsurf',
            self::ANTIGRAVITY => '.agent',
            self::COPILOT => '.github',
            self::KIRO => '.kiro',
            self::CODEX => '.codex',
            self::ROOCODE => '.roo',
            self::QODER => '.qoder',
            self::GEMINI => '.gemini',
            self::TRAE => '.trae',
            self::OPENCODE => '.opencode',
            self::CONTINUE => '.continue',
            self::CODEBUDDY => '.codebuddy',
            self::DROID => '.factory',
            self::IFLOW => '.iflow',
            self::ALL => '',
        };
    }
    
    public function getSkillPath(): string
    {
        return match ($this) {
            self::COPILOT => 'prompts',
            self::KIRO => 'steering',
            default => 'skills',
        };
    }
    
    public function getDisplayName(): string
    {
        return match ($this) {
            self::CLAUDE => 'Claude Code',
            self::CURSOR => 'Cursor',
            self::WINDSURF => 'Windsurf',
            self::ANTIGRAVITY => 'Antigravity',
            self::COPILOT => 'GitHub Copilot',
            self::KIRO => 'Kiro',
            self::CODEX => 'Codex CLI',
            self::ROOCODE => 'Roo Code',
            self::QODER => 'Qoder',
            self::GEMINI => 'Gemini CLI',
            self::TRAE => 'Trae',
            self::OPENCODE => 'OpenCode',
            self::CONTINUE => 'Continue',
            self::CODEBUDDY => 'CodeBuddy',
            self::DROID => 'Droid (Factory)',
            self::IFLOW => 'iFlow CLI',
            self::ALL => 'All AI assistants',
        };
    }
    
    public static function all(): array
    {
        return array_filter(
            self::cases(),
            fn (self $platform) => $platform !== self::ALL
        );
    }
}