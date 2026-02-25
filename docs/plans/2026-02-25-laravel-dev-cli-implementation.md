# Laravel Dev CLI Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Build a Composer global CLI tool for Laravel development with preset-based project creation and AI Agent SKILL installation.

**Architecture:** Layered architecture with Console/Commands for CLI, Services for business logic, Domain for value objects, and Support for utilities. Uses Symfony Console for CLI framework.

**Tech Stack:** PHP 8.2+, Symfony Console, Symfony Filesystem, Symfony Process, Guzzle HTTP

---

## Task 1: Project Initialization

**Files:**
- Create: `composer.json`
- Create: `bin/laravel-dev`
- Create: `.gitignore`
- Create: `phpunit.xml`

**Step 1: Create composer.json**

```json
{
    "name": "x-multibyte/laravel-dev-cli",
    "description": "CLI tool for Laravel development with AI Agent integration",
    "type": "library",
    "license": "MIT",
    "authors": [
        {
            "name": "x-multibyte",
            "email": "dev@x-multibyte.com"
        }
    ],
    "require": {
        "php": "^8.2",
        "symfony/console": "^7.0",
        "symfony/filesystem": "^7.0",
        "symfony/process": "^7.0",
        "guzzlehttp/guzzle": "^7.8"
    },
    "require-dev": {
        "phpunit/phpunit": "^11.0",
        "pestphp/pest": "^3.0"
    },
    "autoload": {
        "psr-4": {
            "LaravelDev\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "bin": [
        "bin/laravel-dev"
    ],
    "config": {
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true
        }
    },
    "minimum-stability": "stable"
}
```

**Step 2: Create bin/laravel-dev**

```php
#!/usr/bin/env php
<?php

use LaravelDev\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$application->run();
```

**Step 3: Create .gitignore**

```
/vendor/
/.idea/
/.vscode/
/phpunit.xml
/.phpunit.cache/
composer.lock
```

**Step 4: Create phpunit.xml**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.0/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         cacheDirectory=".phpunit.cache">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

**Step 5: Install dependencies**

Run: `composer install`
Expected: Dependencies installed successfully

**Step 6: Commit**

```bash
git add composer.json bin/laravel-dev .gitignore phpunit.xml
git commit -m "feat: initialize project with composer.json and entry point"
```

---

## Task 2: Core Application

**Files:**
- Create: `src/Console/Application.php`
- Create: `tests/Unit/Console/ApplicationTest.php`

**Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit\Console;

use LaravelDev\Console\Application;
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
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/pest tests/Unit/Console/ApplicationTest.php`
Expected: FAIL - Class not found

**Step 3: Write implementation**

```php
<?php

namespace LaravelDev\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('laravel-dev', '1.0.0');
        
        $this->registerCommands();
    }
    
    private function registerCommands(): void
    {
        // Commands will be registered here
    }
}
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/pest tests/Unit/Console/ApplicationTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Console/Application.php tests/Unit/Console/ApplicationTest.php
git commit -m "feat: add Application class with name and version"
```

---

## Task 3: AI Platform Enum

**Files:**
- Create: `src/Domain/AIPlatform.php`
- Create: `tests/Unit/Domain/AIPlatformTest.php`

**Step 1: Write the failing test**

```php
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
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/pest tests/Unit/Domain/AIPlatformTest.php`
Expected: FAIL - Class not found

**Step 3: Write implementation**

```php
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
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/pest tests/Unit/Domain/AIPlatformTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Domain/AIPlatform.php tests/Unit/Domain/AIPlatformTest.php
git commit -m "feat: add AIPlatform enum with platform configurations"
```

---

## Task 4: Laravel Version Value Object

**Files:**
- Create: `src/Domain/LaravelVersion.php`
- Create: `tests/Unit/Domain/LaravelVersionTest.php`

**Step 1: Write the failing test**

```php
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
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/pest tests/Unit/Domain/LaravelVersionTest.php`
Expected: FAIL - Class not found

**Step 3: Write implementation**

```php
<?php

namespace LaravelDev\Domain;

final class LaravelVersion
{
    private const SUPPORTED_VERSIONS = ['10', '11', '12'];
    
    public function __construct(
        public readonly int $major,
        public readonly ?int $minor = null
    ) {}
    
    public static function parse(string $version): self
    {
        $parts = explode('.', $version);
        $major = (int) $parts[0];
        $minor = isset($parts[1]) ? (int) $parts[1] : null;
        
        return new self($major, $minor);
    }
    
    public function getDocsPath(): string
    {
        return 'v' . $this->major;
    }
    
    public static function isSupported(string $version): bool
    {
        $major = explode('.', $version)[0];
        return in_array($major, self::SUPPORTED_VERSIONS);
    }
    
    public static function getSupported(): array
    {
        return self::SUPPORTED_VERSIONS;
    }
}
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/pest tests/Unit/Domain/LaravelVersionTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Domain/LaravelVersion.php tests/Unit/Domain/LaravelVersionTest.php
git commit -m "feat: add LaravelVersion value object"
```

---

## Task 5: Preset Value Object

**Files:**
- Create: `src/Domain/Preset.php`
- Create: `tests/Unit/Domain/PresetTest.php`

**Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit\Domain;

use LaravelDev\Domain\Preset;
use PHPUnit\Framework\TestCase;

class PresetTest extends TestCase
{
    private array $sampleData;
    
    protected function setUp(): void
    {
        $this->sampleData = [
            'name' => 'api/12',
            'version' => '1.0.0',
            'description' => 'Laravel 12 API project',
            'laravel' => '^12.0',
            'dependencies' => [
                'composer' => ['laravel/sanctum' => '^4.0'],
                'npm' => [],
            ],
            'dev_dependencies' => [
                'composer' => [],
                'npm' => [],
            ],
            'env_template' => [
                'APP_NAME' => 'Laravel',
            ],
            'commands' => ['php artisan key:generate'],
            'hooks' => [
                'after_install' => ['php artisan migrate'],
            ],
            'metadata' => [
                'tags' => ['api', 'sanctum'],
                'category' => 'api',
            ],
        ];
    }
    
    public function test_from_array_creates_preset(): void
    {
        $preset = Preset::fromArray($this->sampleData);
        
        $this->assertEquals('api/12', $preset->name);
        $this->assertEquals('1.0.0', $preset->version);
        $this->assertEquals('Laravel 12 API project', $preset->description);
    }
    
    public function test_get_category(): void
    {
        $preset = Preset::fromArray($this->sampleData);
        
        $this->assertEquals('api', $preset->getCategory());
    }
    
    public function test_get_laravel_major_version(): void
    {
        $preset = Preset::fromArray($this->sampleData);
        
        $this->assertEquals(12, $preset->getLaravelMajorVersion());
    }
}
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/pest tests/Unit/Domain/PresetTest.php`
Expected: FAIL - Class not found

**Step 3: Write implementation**

```php
<?php

namespace LaravelDev\Domain;

final class Preset
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
    
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            version: $data['version'],
            description: $data['description'],
            laravel: $data['laravel']['version'] ?? $data['laravel'],
            dependencies: $data['dependencies'] ?? [],
            devDependencies: $data['dev_dependencies'] ?? [],
            envTemplate: $data['env_template'] ?? [],
            commands: $data['commands'] ?? [],
            hooks: $data['hooks'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }
    
    public function getCategory(): string
    {
        return $this->metadata['category'] ?? explode('/', $this->name)[0];
    }
    
    public function getLaravelMajorVersion(): int
    {
        $constraint = $this->laravel;
        
        // Extract major version from constraint like "^12.0" or "12.*"
        if (preg_match('/(\d+)/', $constraint, $matches)) {
            return (int) $matches[1];
        }
        
        // Fallback: extract from name like "api/12"
        $parts = explode('/', $this->name);
        return (int) end($parts);
    }
}
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/pest tests/Unit/Domain/PresetTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Domain/Preset.php tests/Unit/Domain/PresetTest.php
git commit -m "feat: add Preset value object"
```

---

## Task 6: Filesystem Support

**Files:**
- Create: `src/Support/Filesystem.php`
- Create: `tests/Unit/Support/FilesystemTest.php`

**Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit\Support;

use LaravelDev\Support\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    private Filesystem $fs;
    private string $tempDir;
    
    protected function setUp(): void
    {
        $this->fs = new Filesystem();
        $this->tempDir = sys_get_temp_dir() . '/laravel-dev-test-' . uniqid();
        mkdir($this->tempDir, 0755, true);
    }
    
    protected function tearDown(): void
    {
        $this->fs->deleteDirectory($this->tempDir);
    }
    
    public function test_ensure_directory_exists(): void
    {
        $path = $this->tempDir . '/new/dir';
        
        $this->fs->ensureDirectoryExists($path);
        
        $this->assertDirectoryExists($path);
    }
    
    public function test_expand_home_path(): void
    {
        $result = $this->fs->expandHomePath('~/test');
        
        $this->assertEquals($_SERVER['HOME'] . '/test', $result);
    }
    
    public function test_write_file(): void
    {
        $path = $this->tempDir . '/test.txt';
        
        $this->fs->write($path, 'Hello World');
        
        $this->assertFileExists($path);
        $this->assertEquals('Hello World', file_get_contents($path));
    }
    
    public function test_delete_directory(): void
    {
        $path = $this->tempDir . '/to-delete';
        mkdir($path . '/nested', 0755, true);
        file_put_contents($path . '/file.txt', 'test');
        
        $this->fs->deleteDirectory($path);
        
        $this->assertDirectoryDoesNotExist($path);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/pest tests/Unit/Support/FilesystemTest.php`
Expected: FAIL - Class not found

**Step 3: Write implementation**

```php
<?php

namespace LaravelDev\Support;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem
{
    private SymfonyFilesystem $fs;
    
    public function __construct()
    {
        $this->fs = new SymfonyFilesystem();
    }
    
    public function ensureDirectoryExists(string $path): void
    {
        if (!$this->fs->exists($path)) {
            $this->fs->mkdir($path, 0755);
        }
    }
    
    public function expandHomePath(string $path): string
    {
        if (str_starts_with($path, '~/')) {
            $home = $_SERVER['HOME'] ?? getenv('HOME');
            return $home . substr($path, 1);
        }
        
        return $path;
    }
    
    public function write(string $path, string $content): void
    {
        $this->fs->dumpFile($path, $content);
    }
    
    public function read(string $path): string
    {
        return file_get_contents($path);
    }
    
    public function exists(string $path): bool
    {
        return $this->fs->exists($path);
    }
    
    public function deleteDirectory(string $path): void
    {
        if ($this->fs->exists($path)) {
            $this->fs->remove($path);
        }
    }
    
    public function copyDirectory(string $source, string $destination): void
    {
        $this->fs->mirror($source, $destination);
    }
}
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/pest tests/Unit/Support/FilesystemTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Support/Filesystem.php tests/Unit/Support/FilesystemTest.php
git commit -m "feat: add Filesystem support class"
```

---

## Task 7: HTTP Client Support

**Files:**
- Create: `src/Support/HttpClient.php`
- Create: `tests/Unit/Support/HttpClientTest.php`

**Step 1: Write the failing test**

```php
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
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/pest tests/Unit/Support/HttpClientTest.php`
Expected: FAIL - Class not found

**Step 3: Write implementation**

```php
<?php

namespace LaravelDev\Support;

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
                'User-Agent' => 'laravel-dev-cli/1.0.0',
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
        return json_decode($body, true);
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
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/pest tests/Unit/Support/HttpClientTest.php`
Expected: PASS (may need network)

**Step 5: Commit**

```bash
git add src/Support/HttpClient.php tests/Unit/Support/HttpClientTest.php
git commit -m "feat: add HttpClient support class"
```

---

## Task 8: AI Detector Service

**Files:**
- Create: `src/Services/AIDetector.php`
- Create: `tests/Unit/Services/AIDetectorTest.php`

**Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit\Services;

use LaravelDev\Domain\AIPlatform;
use LaravelDev\Services\AIDetector;
use PHPUnit\Framework\TestCase;

class AIDetectorTest extends TestCase
{
    private AIDetector $detector;
    private string $tempDir;
    
    protected function setUp(): void
    {
        $this->detector = new AIDetector();
        $this->tempDir = sys_get_temp_dir() . '/laravel-dev-detector-' . uniqid();
        mkdir($this->tempDir, 0755, true);
    }
    
    protected function tearDown(): void
    {
        exec("rm -rf {$this->tempDir}");
    }
    
    public function test_detect_returns_empty_array_when_no_platforms(): void
    {
        $detected = $this->detector->detect($this->tempDir);
        
        $this->assertEmpty($detected);
    }
    
    public function test_detect_finds_claude(): void
    {
        mkdir($this->tempDir . '/.claude', 0755, true);
        
        $detected = $this->detector->detect($this->tempDir);
        
        $this->assertCount(1, $detected);
        $this->assertEquals(AIPlatform::CLAUDE, $detected[0]);
    }
    
    public function test_detect_finds_multiple_platforms(): void
    {
        mkdir($this->tempDir . '/.claude', 0755, true);
        mkdir($this->tempDir . '/.cursor', 0755, true);
        
        $detected = $this->detector->detect($this->tempDir);
        
        $this->assertCount(2, $detected);
        $this->assertContains(AIPlatform::CLAUDE, $detected);
        $this->assertContains(AIPlatform::CURSOR, $detected);
    }
    
    public function test_get_skill_path_returns_correct_path(): void
    {
        $path = $this->detector->getSkillPath(AIPlatform::CLAUDE, $this->tempDir);
        
        $this->assertEquals($this->tempDir . '/.claude/skills/laravel-dev', $path);
    }
    
    public function test_get_supported_platforms_returns_all(): void
    {
        $platforms = $this->detector->getSupportedPlatforms();
        
        $this->assertNotEmpty($platforms);
        $this->assertNotContains(AIPlatform::ALL, $platforms);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/pest tests/Unit/Services/AIDetectorTest.php`
Expected: FAIL - Class not found

**Step 3: Write implementation**

```php
<?php

namespace LaravelDev\Services;

use LaravelDev\Domain\AIPlatform;

class AIDetector
{
    public function detect(string $projectPath): array
    {
        $detected = [];
        
        foreach (AIPlatform::all() as $platform) {
            $configFolder = $platform->getConfigFolder();
            if ($configFolder && is_dir($projectPath . '/' . $configFolder)) {
                $detected[] = $platform;
            }
        }
        
        return $detected;
    }
    
    public function getSkillPath(AIPlatform $platform, string $projectPath): string
    {
        return sprintf(
            '%s/%s/%s/laravel-dev',
            $projectPath,
            $platform->getConfigFolder(),
            $platform->getSkillPath()
        );
    }
    
    public function getSupportedPlatforms(): array
    {
        return AIPlatform::all();
    }
    
    public function getSuggestedPlatform(array $detected): ?AIPlatform
    {
        if (count($detected) === 1) {
            return $detected[0];
        }
        
        if (count($detected) > 1) {
            return AIPlatform::ALL;
        }
        
        return null;
    }
}
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/pest tests/Unit/Services/AIDetectorTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Services/AIDetector.php tests/Unit/Services/AIDetectorTest.php
git commit -m "feat: add AIDetector service"
```

---

## Task 9: Preset Service

**Files:**
- Create: `src/Services/PresetService.php`
- Create: `tests/Unit/Services/PresetServiceTest.php`

**Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit\Services;

use LaravelDev\Services\PresetService;
use PHPUnit\Framework\TestCase;

class PresetServiceTest extends TestCase
{
    private PresetService $service;
    private string $cachePath;
    
    protected function setUp(): void
    {
        $this->cachePath = sys_get_temp_dir() . '/laravel-dev-presets-' . uniqid();
        mkdir($this->cachePath . '/api', 0755, true);
        mkdir($this->cachePath . '/framework', 0755, true);
        
        // Create sample preset files
        file_put_contents(
            $this->cachePath . '/api/12.json',
            json_encode([
                'name' => 'api/12',
                'version' => '1.0.0',
                'description' => 'Laravel 12 API',
                'laravel' => ['version' => '^12.0'],
                'dependencies' => ['composer' => ['laravel/sanctum' => '^4.0']],
            ])
        );
        
        file_put_contents(
            $this->cachePath . '/framework/12.json',
            json_encode([
                'name' => 'framework/12',
                'version' => '1.0.0',
                'description' => 'Laravel 12 Framework',
                'laravel' => ['version' => '^12.0'],
            ])
        );
        
        $this->service = new PresetService($this->cachePath);
    }
    
    protected function tearDown(): void
    {
        exec("rm -rf {$this->cachePath}");
    }
    
    public function test_list_returns_all_presets(): void
    {
        $presets = $this->service->list();
        
        $this->assertCount(2, $presets);
    }
    
    public function test_list_filters_by_category(): void
    {
        $presets = $this->service->list(category: 'api');
        
        $this->assertCount(1, $presets);
        $this->assertEquals('api/12', $presets[0]->name);
    }
    
    public function test_get_returns_preset(): void
    {
        $preset = $this->service->get('api/12');
        
        $this->assertNotNull($preset);
        $this->assertEquals('Laravel 12 API', $preset->description);
    }
    
    public function test_get_returns_null_for_unknown(): void
    {
        $preset = $this->service->get('unknown/preset');
        
        $this->assertNull($preset);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/pest tests/Unit/Services/PresetServiceTest.php`
Expected: FAIL - Class not found

**Step 3: Write implementation**

```php
<?php

namespace LaravelDev\Services;

use LaravelDev\Domain\Preset;
use LaravelDev\Support\Filesystem;

class PresetService
{
    public const DEFAULT_CACHE_PATH = '~/.laravel-dev/presets';
    
    private string $cachePath;
    private Filesystem $fs;
    
    public function __construct(?string $cachePath = null)
    {
        $this->fs = new Filesystem();
        $this->cachePath = $this->fs->expandHomePath($cachePath ?? self::DEFAULT_CACHE_PATH);
    }
    
    public function list(?string $category = null, ?string $laravel = null): array
    {
        $presets = [];
        
        if (!is_dir($this->cachePath)) {
            return $presets;
        }
        
        $categories = $category ? [$category] : $this->getCategories();
        
        foreach ($categories as $cat) {
            $catPath = $this->cachePath . '/' . $cat;
            if (!is_dir($catPath)) {
                continue;
            }
            
            foreach (glob($catPath . '/*.json') as $file) {
                $preset = $this->loadPreset($file);
                
                if ($preset) {
                    if ($laravel === null || $preset->getLaravelMajorVersion() === (int) $laravel) {
                        $presets[] = $preset;
                    }
                }
            }
        }
        
        return $presets;
    }
    
    public function get(string $name): ?Preset
    {
        $parts = explode('/', $name);
        if (count($parts) !== 2) {
            return null;
        }
        
        [$category, $version] = $parts;
        $file = $this->cachePath . '/' . $category . '/' . $version . '.json';
        
        if (!file_exists($file)) {
            return null;
        }
        
        return $this->loadPreset($file);
    }
    
    public function getCachePath(): string
    {
        return $this->cachePath;
    }
    
    private function getCategories(): array
    {
        $categories = [];
        
        foreach (glob($this->cachePath . '/*', GLOB_ONLYDIR) as $dir) {
            $categories[] = basename($dir);
        }
        
        return $categories;
    }
    
    private function loadPreset(string $file): ?Preset
    {
        $content = file_get_contents($file);
        if ($content === false) {
            return null;
        }
        
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        
        return Preset::fromArray($data);
    }
}
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/pest tests/Unit/Services/PresetServiceTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Services/PresetService.php tests/Unit/Services/PresetServiceTest.php
git commit -m "feat: add PresetService"
```

---

## Task 10: Skill Installer Service

**Files:**
- Create: `src/Services/SkillInstaller.php`
- Create: `tests/Unit/Services/SkillInstallerTest.php`

**Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit\Services;

use LaravelDev\Domain\AIPlatform;
use LaravelDev\Services\SkillInstaller;
use PHPUnit\Framework\TestCase;

class SkillInstallerTest extends TestCase
{
    private SkillInstaller $installer;
    private string $tempDir;
    private string $globalPath;
    
    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/laravel-dev-skill-test-' . uniqid();
        $this->globalPath = $this->tempDir . '/global';
        $this->installer = new SkillInstaller($this->globalPath);
        
        mkdir($this->tempDir . '/project', 0755, true);
    }
    
    protected function tearDown(): void
    {
        exec("rm -rf {$this->tempDir}");
    }
    
    public function test_ensure_global_presets_creates_directory(): void
    {
        $this->installer->ensureGlobalPresets();
        
        $this->assertDirectoryExists($this->globalPath . '/presets');
    }
    
    public function test_get_skill_target_path(): void
    {
        $path = $this->installer->getSkillTargetPath(
            AIPlatform::CLAUDE,
            $this->tempDir . '/project'
        );
        
        $this->assertEquals(
            $this->tempDir . '/project/.claude/skills/laravel-dev',
            $path
        );
    }
    
    public function test_get_global_presets_path(): void
    {
        $path = $this->installer->getGlobalPresetsPath();
        
        $this->assertEquals($this->globalPath . '/presets', $path);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/pest tests/Unit/Services/SkillInstallerTest.php`
Expected: FAIL - Class not found

**Step 3: Write implementation**

```php
<?php

namespace LaravelDev\Services;

use LaravelDev\Domain\AIPlatform;
use LaravelDev\Support\Filesystem;

class SkillInstaller
{
    public const SKILL_REPO = 'x-multibyte/laravel-dev-skill';
    public const PRESETS_REPO = 'x-multibyte/laravel-dev-presets';
    
    private string $globalPath;
    private Filesystem $fs;
    
    public function __construct(?string $globalPath = null)
    {
        $this->fs = new Filesystem();
        $this->globalPath = $this->fs->expandHomePath(
            $globalPath ?? '~/.laravel-dev'
        );
    }
    
    public function ensureGlobalPresets(): void
    {
        $presetsPath = $this->getGlobalPresetsPath();
        $this->fs->ensureDirectoryExists($presetsPath);
    }
    
    public function getSkillTargetPath(AIPlatform $platform, string $projectPath): string
    {
        return sprintf(
            '%s/%s/%s/laravel-dev',
            $projectPath,
            $platform->getConfigFolder(),
            $platform->getSkillPath()
        );
    }
    
    public function getGlobalPresetsPath(): string
    {
        return $this->globalPath . '/presets';
    }
    
    public function getGlobalSkillPath(): string
    {
        return $this->globalPath . '/skill';
    }
    
    public function install(AIPlatform $platform, string $projectPath, bool $force = false): array
    {
        // Ensure global directories exist
        $this->ensureGlobalPresets();
        
        // Get target path
        $targetPath = $this->getSkillTargetPath($platform, $projectPath);
        
        // Check if already exists
        if ($this->fs->exists($targetPath) && !$force) {
            throw new \RuntimeException("SKILL already installed at: {$targetPath}. Use --force to overwrite.");
        }
        
        // Create target directory
        $this->fs->ensureDirectoryExists($targetPath);
        
        // Return installed path
        return [$targetPath];
    }
}
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/pest tests/Unit/Services/SkillInstallerTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Services/SkillInstaller.php tests/Unit/Services/SkillInstallerTest.php
git commit -m "feat: add SkillInstaller service"
```

---

## Task 11: Skill Command

**Files:**
- Create: `src/Console/Commands/SkillCommand.php`
- Modify: `src/Console/Application.php`

**Step 1: Write minimal implementation**

```php
<?php

namespace LaravelDev\Console\Commands;

use LaravelDev\Domain\AIPlatform;
use LaravelDev\Services\AIDetector;
use LaravelDev\Services\SkillInstaller;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(
    name: 'skill',
    description: 'Install Laravel Dev SKILL to AI coding assistant'
)]
class SkillCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('ai', 'a', InputOption::VALUE_OPTIONAL, 'Target AI platform')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing SKILL')
            ->addOption('offline', null, InputOption::VALUE_NONE, 'Use local cache')
            ->addOption('no-presets', null, InputOption::VALUE_NONE, 'Skip preset sync');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectPath = getcwd();
        $detector = new AIDetector();
        $installer = new SkillInstaller();
        
        // Detect installed platforms
        $detected = $detector->detect($projectPath);
        
        // Get target platform
        $aiType = $input->getOption('ai');
        
        if ($aiType) {
            $platform = AIPlatform::tryFrom($aiType);
            if (!$platform) {
                $output->writeln("<error>Invalid AI platform: {$aiType}</error>");
                return Command::FAILURE;
            }
        } else {
            // Interactive selection
            $platforms = AIPlatform::all();
            $choices = array_map(
                fn(AIPlatform $p) => $p->getDisplayName() . (in_array($p, $detected) ? ' (detected)' : ''),
                $platforms
            );
            $choices[] = AIPlatform::ALL->getDisplayName();
            
            $question = new ChoiceQuestion(
                'Select AI assistant to install for:',
                $choices,
                0
            );
            
            $helper = $this->getHelper('question');
            $selected = $helper->ask($input, $output, $question);
            $selectedIndex = array_search($selected, $choices);
            $platform = $selectedIndex < count($platforms) 
                ? $platforms[$selectedIndex] 
                : AIPlatform::ALL;
        }
        
        $output->writeln("<info>Installing for: {$platform->getDisplayName()}</info>");
        
        try {
            $force = $input->getOption('force');
            
            if ($platform === AIPlatform::ALL) {
                $platforms = $detector->getSupportedPlatforms();
                foreach ($platforms as $p) {
                    $installer->install($p, $projectPath, $force);
                }
            } else {
                $installer->install($platform, $projectPath, $force);
            }
            
            $output->writeln('');
            $output->writeln('<info>✓ SKILL installed successfully!</info>');
            $output->writeln('');
            $output->writeln('<comment>Next steps:</comment>');
            $output->writeln('  1. Restart your AI coding assistant');
            $output->writeln('  2. Try: "Create a Laravel 12 API project"');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
```

**Step 2: Update Application.php**

```php
<?php

namespace LaravelDev\Console;

use LaravelDev\Console\Commands\SkillCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('laravel-dev', '1.0.0');
        
        $this->registerCommands();
    }
    
    private function registerCommands(): void
    {
        $this->add(new SkillCommand());
    }
}
```

**Step 3: Test manually**

Run: `php bin/laravel-dev skill --help`
Expected: Show command help

**Step 4: Commit**

```bash
git add src/Console/Commands/SkillCommand.php src/Console/Application.php
git commit -m "feat: add skill command"
```

---

## Task 12: Preset List Command

**Files:**
- Create: `src/Console/Commands/PresetListCommand.php`
- Modify: `src/Console/Application.php`

**Step 1: Write implementation**

```php
<?php

namespace LaravelDev\Console\Commands;

use LaravelDev\Services\PresetService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'preset:list',
    description: 'List all available presets'
)]
class PresetListCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('category', 'c', InputOption::VALUE_OPTIONAL, 'Filter by category')
            ->addOption('laravel', 'l', InputOption::VALUE_OPTIONAL, 'Filter by Laravel version')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output as JSON');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $service = new PresetService();
        
        $presets = $service->list(
            category: $input->getOption('category'),
            laravel: $input->getOption('laravel')
        );
        
        if ($input->getOption('json')) {
            $data = array_map(fn($p) => [
                'name' => $p->name,
                'version' => $p->version,
                'description' => $p->description,
                'category' => $p->getCategory(),
                'laravel' => $p->laravel,
            ], $presets);
            
            $output->writeln(json_encode($data, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        }
        
        if (empty($presets)) {
            $output->writeln('<comment>No presets found.</comment>');
            $output->writeln('<comment>Run `laravel-dev preset:sync` to download presets.</comment>');
            return Command::SUCCESS;
        }
        
        $table = new Table($output);
        $table->setHeaders(['Name', 'Description', 'Laravel']);
        
        foreach ($presets as $preset) {
            $table->addRow([
                $preset->name,
                $preset->description,
                $preset->laravel,
            ]);
        }
        
        $table->render();
        
        return Command::SUCCESS;
    }
}
```

**Step 2: Update Application to register command**

Add to `registerCommands()`:
```php
$this->add(new PresetListCommand());
```

**Step 3: Test manually**

Run: `php bin/laravel-dev preset:list`
Expected: Show presets table or "No presets found" message

**Step 4: Commit**

```bash
git add src/Console/Commands/PresetListCommand.php src/Console/Application.php
git commit -m "feat: add preset:list command"
```

---

## Task 13: New Command

**Files:**
- Create: `src/Console/Commands/NewCommand.php`
- Modify: `src/Console/Application.php`

**Step 1: Write implementation**

```php
<?php

namespace LaravelDev\Console\Commands;

use LaravelDev\Domain\AIPlatform;
use LaravelDev\Services\PresetService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'new',
    description: 'Create a new Laravel project with preset'
)]
class NewCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Project name')
            ->addOption('preset', 'p', InputOption::VALUE_OPTIONAL, 'Preset name (e.g., api/12)')
            ->addOption('laravel', 'l', InputOption::VALUE_OPTIONAL, 'Laravel version (10, 11, 12)')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'Installation path')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing directory')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output as JSON');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectName = $input->getArgument('name');
        $path = $input->getOption('path') ?? getcwd();
        $projectPath = $path . '/' . $projectName;
        
        $presetService = new PresetService();
        
        // Get preset
        $presetName = $input->getOption('preset');
        
        if (!$presetName) {
            $presets = $presetService->list(
                laravel: $input->getOption('laravel')
            );
            
            if (empty($presets)) {
                $output->writeln('<error>No presets available. Run `laravel-dev preset:sync` first.</error>');
                return Command::FAILURE;
            }
            
            $choices = array_map(fn($p) => "{$p->name} - {$p->description}", $presets);
            $question = new ChoiceQuestion('Select preset:', $choices, 0);
            
            $helper = $this->getHelper('question');
            $selected = $helper->ask($input, $output, $question);
            $selectedIndex = array_search($selected, $choices);
            $presetName = $presets[$selectedIndex]->name;
        }
        
        $preset = $presetService->get($presetName);
        
        if (!$preset) {
            $output->writeln("<error>Preset not found: {$presetName}</error>");
            return Command::FAILURE;
        }
        
        // Confirm
        $output->writeln("<info>Preset: {$preset->name}</info>");
        $output->writeln("<comment>{$preset->description}</comment>");
        
        if (!$input->getOption('json')) {
            $question = new ConfirmationQuestion('Continue? [y/N] ', false);
            $helper = $this->getHelper('question');
            
            if (!$helper->ask($input, $output, $question)) {
                $output->writeln('<comment>Cancelled.</comment>');
                return Command::SUCCESS;
            }
        }
        
        // Create project
        $output->writeln("<info>Creating Laravel project...</info>");
        
        $laravelVersion = $preset->getLaravelMajorVersion();
        $process = new Process([
            'composer', 'create-project',
            'laravel/laravel',
            $projectPath,
            "--prefer-dist",
        ]);
        
        $process->setTty(true);
        $process->run();
        
        if (!$process->isSuccessful()) {
            $output->writeln('<error>Failed to create project.</error>');
            return Command::FAILURE;
        }
        
        $output->writeln('');
        $output->writeln('<info>✓ Project created successfully!</info>');
        $output->writeln("<comment>Project path: {$projectPath}</comment>");
        
        return Command::SUCCESS;
    }
}
```

**Step 2: Update Application**

Add to `registerCommands()`:
```php
$this->add(new NewCommand());
```

**Step 3: Test manually**

Run: `php bin/laravel-dev new test-project --preset=framework/12`
Expected: Start creating Laravel project (may fail if no presets synced)

**Step 4: Commit**

```bash
git add src/Console/Commands/NewCommand.php src/Console/Application.php
git commit -m "feat: add new command for project creation"
```

---

## Task 14: Docs Command

**Files:**
- Create: `src/Console/Commands/DocsCommand.php`
- Create: `src/Services/DocsService.php`
- Modify: `src/Console/Application.php`

**Step 1: Write DocsService**

```php
<?php

namespace LaravelDev\Services;

use LaravelDev\Support\Filesystem;

class DocsService
{
    private const VERSIONS = ['10', '11', '12'];
    
    private const TOPIC_MAP = [
        'routing' => 'routing.md',
        'database' => 'database.md',
        'eloquent' => 'database.md',
        'auth' => 'auth.md',
        'authentication' => 'auth.md',
        'cache' => 'cache.md',
        'queues' => 'queues.md',
        'testing' => 'testing.md',
        'artisan' => 'artisan.md',
        'configuration' => 'configuration.md',
        'structure' => 'structure.md',
        'views' => 'views.md',
        'blade' => 'views.md',
        'mail' => 'mail.md',
        'notifications' => 'notifications.md',
        'events' => 'events.md',
        'security' => 'security.md',
        'errors' => 'errors.md',
        'logging' => 'logging.md',
        'helpers' => 'helpers.md',
    ];
    
    private Filesystem $fs;
    private string $docsPath;
    
    public function __construct(?string $docsPath = null)
    {
        $this->fs = new Filesystem();
        $this->docsPath = $this->fs->expandHomePath(
            $docsPath ?? '~/.laravel-dev/skill/references'
        );
    }
    
    public function get(string $topic, string $version = '12'): ?string
    {
        $filename = self::TOPIC_MAP[strtolower($topic)] ?? null;
        
        if (!$filename) {
            return null;
        }
        
        $filePath = $this->docsPath . '/v' . $version . '/' . $filename;
        
        if (!file_exists($filePath)) {
            return null;
        }
        
        return file_get_contents($filePath);
    }
    
    public function listTopics(): array
    {
        return array_keys(self::TOPIC_MAP);
    }
    
    public function getVersions(): array
    {
        return self::VERSIONS;
    }
}
```

**Step 2: Write DocsCommand**

```php
<?php

namespace LaravelDev\Console\Commands;

use LaravelDev\Services\DocsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'docs',
    description: 'Query Laravel documentation'
)]
class DocsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('topic', InputArgument::OPTIONAL, 'Documentation topic')
            ->addOption('version', 'v', InputOption::VALUE_OPTIONAL, 'Laravel version', '12')
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'List available topics');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $service = new DocsService();
        
        if ($input->getOption('list')) {
            $output->writeln('<info>Available topics:</info>');
            foreach ($service->listTopics() as $topic) {
                $output->writeln("  - {$topic}");
            }
            $output->writeln('');
            $output->writeln('<info>Available versions:</info>');
            foreach ($service->getVersions() as $version) {
                $output->writeln("  - {$version}");
            }
            return Command::SUCCESS;
        }
        
        $topic = $input->getArgument('topic');
        
        if (!$topic) {
            $output->writeln('<error>Please specify a topic or use --list</error>');
            return Command::FAILURE;
        }
        
        $version = $input->getOption('version');
        $content = $service->get($topic, $version);
        
        if (!$content) {
            $output->writeln("<error>Topic not found: {$topic} (version {$version})</error>");
            return Command::FAILURE;
        }
        
        $output->writeln($content);
        
        return Command::SUCCESS;
    }
}
```

**Step 3: Update Application**

Add to `registerCommands()`:
```php
$this->add(new DocsCommand());
```

**Step 4: Test manually**

Run: `php bin/laravel-dev docs --list`
Expected: Show available topics

**Step 5: Commit**

```bash
git add src/Services/DocsService.php src/Console/Commands/DocsCommand.php src/Console/Application.php
git commit -m "feat: add docs command and service"
```

---

## Task 15: Final Integration & Testing

**Files:**
- Run all tests
- Create: `README.md` (if requested)

**Step 1: Run all tests**

Run: `./vendor/bin/pest`
Expected: All tests pass

**Step 2: Test CLI commands**

```bash
php bin/laravel-dev --version
php bin/laravel-dev --help
php bin/laravel-dev skill --help
php bin/laravel-dev preset:list
php bin/laravel-dev docs --list
```

**Step 3: Final commit**

```bash
git add -A
git commit -m "chore: final integration and testing"
```

---

## Summary

This implementation plan covers:

1. **Project Setup** - composer.json, entry point, dependencies
2. **Domain Models** - AIPlatform enum, LaravelVersion, Preset
3. **Support Classes** - Filesystem, HttpClient
4. **Services** - AIDetector, PresetService, SkillInstaller, DocsService
5. **Commands** - skill, preset:list, new, docs

Total: 15 tasks with TDD approach, frequent commits, and bite-sized steps.
