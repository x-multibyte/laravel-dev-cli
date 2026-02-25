# Laravel Dev CLI - Design Document

**Date:** 2026-02-25  
**Status:** Approved  
**Author:** AI Agent (iFlow CLI)

---

## Overview

Laravel Dev CLI is a Composer global tool that provides developers with a seamless experience for Laravel development, integrated with AI Agent CLIs. It serves as the entry point for the preset system and SKILL installation.

### Core Value Proposition

- **Preset-based Project Creation** - Quick start with pre-configured project templates
- **AI Agent Integration** - Install SKILL to various AI coding assistants
- **Knowledge Injection** - Help AI agents access correct Laravel documentation
- **Developer-First** - Interactive CLI with excellent UX

---

## Architecture

### Project Structure

```
laravel-dev-cli/
├── bin/
│   └── laravel-dev                 # Executable entry point
│
├── src/
│   ├── Console/
│   │   ├── Application.php             # Symfony Console Application
│   │   └── Commands/
│   │       ├── NewCommand.php          # Create new project
│   │       ├── PresetCommand.php       # Preset management
│   │       ├── DocsCommand.php         # Documentation query
│   │       └── SkillCommand.php        # SKILL installation
│   │
│   ├── Services/
│   │   ├── PresetService.php           # Preset logic
│   │   ├── SkillInstaller.php          # SKILL installation logic
│   │   ├── AIDetector.php              # AI Agent detection
│   │   ├── DocsService.php             # Documentation indexing
│   │   └── ProjectGenerator.php        # Project generation
│   │
│   ├── Domain/
│   │   ├── Preset.php                  # Preset value object
│   │   ├── AIPlatform.php              # AI platform enum
│   │   └── LaravelVersion.php          # Laravel version value object
│   │
│   └── Support/
│       ├── HttpClient.php              # HTTP request wrapper
│       ├── Filesystem.php              # File operations
│       └── OutputHelper.php            # Output formatting
│
├── templates/
│   └── skill/                          # SKILL template files
│
├── config/
│   └── platforms.php                   # AI platform configuration
│
├── composer.json
└── README.md
```

### Dependencies

| Package | Purpose |
|---------|---------|
| `symfony/console` | CLI framework |
| `symfony/filesystem` | File operations |
| `symfony/process` | Process execution |
| `guzzlehttp/guzzle` | HTTP client |
| `nette/php-generator` | PHP code generation (optional) |

---

## Commands

### Command Overview

| Command | Description | Example |
|---------|-------------|---------|
| `new` | Create project with preset | `laravel-dev new my-api --preset=api/12` |
| `preset:list` | List available presets | `laravel-dev preset:list` |
| `preset:show` | Show preset details | `laravel-dev preset:show api/12` |
| `preset:validate` | Validate preset config | `laravel-dev preset:validate api/12` |
| `docs` | Query Laravel docs | `laravel-dev docs routing --version=12` |
| `skill` | Install SKILL to AI Agent | `laravel-dev skill --ai=claude` |
| `self-update` | Update CLI itself | `laravel-dev self-update` |

### `new` Command

```bash
laravel-dev new <project-name> [options]

Options:
  --preset=NAME       Specify preset (default: interactive selection)
  --laravel=VERSION   Laravel version (10, 11, 12)
  --path=PATH         Creation path (default: current directory)
  --force             Overwrite existing directory
  --no-interaction    Non-interactive mode
  --json              JSON output (for AI Agent calls)
```

**Interactive Flow:**
1. If `--preset` not specified, show preset list for selection
2. Display preset summary, confirm to continue
3. Call `composer create-project` to create project
4. Apply preset configuration (dependencies, env vars, commands)
5. Output next steps

### `skill` Command

```bash
laravel-dev skill [options]

Options:
  --ai=TYPE          Target AI platform (claude, cursor, windsurf, all...)
  --force            Overwrite existing SKILL
  --offline          Use local cache
  --no-presets       Skip preset sync
```

---

## Services

### PresetService

Responsible for preset fetching, validation, and application.

```php
class PresetService
{
    const PRESETS_REPO = 'x-multibyte/laravel-dev-presets';
    const CACHE_PATH = '~/.laravel-dev/presets';
    
    public function list(?string $category, ?string $laravel): array;
    public function get(string $name): ?Preset;
    public function validate(string $name): array;
    public function apply(Preset $preset, string $projectPath): void;
    public function sync(): void;
    public function checkDependencies(Preset $preset): array;
}
```

### SkillInstaller

Handles SKILL download and installation to AI platforms.

```php
class SkillInstaller
{
    const SKILL_REPO = 'x-multibyte/laravel-dev-skill';
    
    public function install(AIPlatform $platform, bool $force): array;
    private function download(): string;
    private function generatePlatformFiles(AIPlatform $platform, string $targetDir): void;
    private function ensureGlobalPresets(): void;
}
```

### AIDetector

Detects installed AI Agent CLIs in project.

```php
class AIDetector
{
    private const PLATFORMS = [
        'claude' => ['folder' => '.claude', 'skillPath' => 'skills'],
        'cursor' => ['folder' => '.cursor', 'skillPath' => 'skills'],
        'windsurf' => ['folder' => '.windsurf', 'skillPath' => 'skills'],
        // ... more platforms
    ];
    
    public function detect(string $projectPath): array;
    public function getSkillPath(AIPlatform $platform, string $projectPath): string;
    public function getSupportedPlatforms(): array;
}
```

### DocsService

Manages Laravel documentation access.

```php
class DocsService
{
    private const VERSIONS = ['10', '11', '12'];
    private const TOPIC_MAP = [
        'routing' => 'routing.md',
        'database' => 'database.md',
        'auth' => 'auth.md',
        // ...
    ];
    
    public function get(string $topic, string $version = '12'): ?string;
    public function listTopics(): array;
    public function search(string $query, string $version = '12'): array;
}
```

---

## Domain Models

### Preset

```php
class Preset
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $description,
        public readonly string $laravel,
        public readonly array $dependencies,
        public readonly array $devDependencies,
        public readonly array $envTemplate,
        public readonly array $commands,
        public readonly array $hooks,
        public readonly array $metadata,
    ) {}
    
    public static function fromArray(array $data): self;
    public function getCategory(): string;
    public function getLaravelMajorVersion(): int;
}
```

### AIPlatform

```php
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
    case ALL = 'all';
    
    public function getConfigFolder(): string;
    public function getSkillPath(): string;
    public function getDisplayName(): string;
}
```

### LaravelVersion

```php
class LaravelVersion
{
    public function __construct(
        public readonly int $major,
        public readonly ?int $minor = null
    ) {}
    
    public static function parse(string $version): self;
    public function getDocsPath(): string;
    public static function isSupported(string $version): bool;
    public static function getSupported(): array;
}
```

---

## SKILL Installation Flow

### Flow Diagram

```
User: laravel-dev skill
         │
         ▼
    ┌─────────────────┐
    │ Detect AI Agent │
    │ (.claude/, etc) │
    └────────┬────────┘
             │
             ▼
    ┌─────────────────┐
    │ Select Platform │
    │ (interactive)   │
    └────────┬────────┘
             │
             ▼
    ┌─────────────────┐
    │ Ensure Global   │
    │ Presets Cache   │
    │ ~/.laravel-dev/ │
    └────────┬────────┘
             │
             ▼
    ┌─────────────────┐
    │ Download SKILL  │
    │ from GitHub     │
    └────────┬────────┘
             │
             ▼
    ┌─────────────────┐
    │ Generate Files  │
    │ to target path  │
    └────────┬────────┘
             │
             ▼
    ┌─────────────────┐
    │ Output Success  │
    │ & Next Steps    │
    └─────────────────┘
```

### Platform Installation Mapping

| Platform | SKILL Location | presets Pointer |
|----------|---------------|-----------------|
| Claude Code | `.claude/skills/laravel-dev/` | `~/.laravel-dev/presets/` |
| Cursor | `.cursor/skills/laravel-dev/` | `~/.laravel-dev/presets/` |
| Windsurf | `.windsurf/skills/laravel-dev/` | `~/.laravel-dev/presets/` |
| Continue | `.continue/skills/laravel-dev/` | `~/.laravel-dev/presets/` |
| Droid (Factory) | `.factory/skills/laravel-dev/` | `~/.laravel-dev/presets/` |

### Global Cache Structure

```
~/.laravel-dev/
├── presets/                    # Preset cache
│   ├── api/
│   ├── filament/
│   ├── framework/
│   └── ...
├── skill/                      # SKILL cache
│   ├── references/
│   │   ├── v10/
│   │   ├── v11/
│   │   ├── v12/
│   │   └── current → v12/
│   └── ...
└── config.json                 # Global config
```

---

## Integration with Existing Projects

### Relationship

```
┌─────────────────────────────────────────────────────────────┐
│  laravel-dev-cli (Composer global tool)                     │
│  → Entry point for developers and AI Agents                 │
└─────────────────────────────────────────────────────────────┘
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
┌─────────────┐   ┌─────────────┐   ┌─────────────┐
│ ~/.laravel- │   │ laravel-    │   │ laravel-    │
│ dev/        │   │ dev-skill   │   │ dev-presets │
│             │   │ (GitHub)    │   │ (GitHub)    │
│ presets/    │   │             │   │             │
│ (local)     │   │ docs+scripts│   │ preset JSON │
└─────────────┘   └─────────────┘   └─────────────┘
```

### User Workflow

```bash
# 1. Install CLI
composer global require x-multibyte/laravel-dev-cli

# 2. Install SKILL (interactive)
cd my-project
laravel-dev skill
# → Detected: .claude/, .cursor/
# → Select target: Claude Code
# → Download skill to .claude/skills/laravel-dev/
# → presets points to ~/.laravel-dev/presets/

# 3. Use
laravel-dev new my-api --preset=api/12
```

---

## Technology Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Language | PHP 8.2+ | Laravel ecosystem consistency |
| CLI Framework | Symfony Console | Proven, lightweight, excellent UX |
| HTTP Client | Guzzle | Reliable, widely used |
| File Operations | Symfony Filesystem | Cross-platform support |

---

## Next Steps

1. Implement core infrastructure (Application, Service container)
2. Implement AIDetector and platform configuration
3. Implement PresetService with remote sync
4. Implement SkillInstaller with template generation
5. Implement commands (new, preset:*, docs, skill)
6. Add unit and integration tests
7. Create Composer package and publish to Packagist

---

## References

- [UI UX Pro Max Skill](https://github.com/nextlevelbuilder/ui-ux-pro-max-skill) - Reference implementation for AI skill installation
- [Laravel Dev Skill](https://github.com/x-multibyte/laravel-dev-skill) - SKILL repository
- [Laravel Dev Presets](https://github.com/x-multibyte/laravel-dev-presets) - Preset repository
