# Git-based Preset Management Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Refactor preset management to use Git for downloading/updating presets with interactive CLI using Laravel Prompts.

**Architecture:** 
- Use Symfony Process to execute Git commands (clone/fetch/reset)
- Store presets in `~/.laravel-dev/presets/` as a Git repository
- Config management via `~/.laravel-dev/config/presets.json`
- Interactive commands using Laravel Prompts

**Tech Stack:** PHP 8.2, Symfony Console, Symfony Process, Laravel Prompts

---

## Storage Structure

```
~/.laravel-dev/
├── config/
│   └── presets.json          # Configuration file
└── presets/                  # Git repository
    ├── .git/
    ├── api/
    │   ├── 10.json
    │   ├── 11.json
    │   └── 12.json
    ├── filament/
    ├── framework/
    ├── laravel-package/
    └── starter-kits/
```

## Configuration File Format

```json
{
  "repository": "https://github.com/x-multibyte/laravel-dev-presets.git",
  "branch": "main",
  "auto_update": true,
  "last_updated": "2026-02-27T10:00:00Z"
}
```

## Commands

| Command | Description |
|---------|-------------|
| `laravel-dev presets` | List presets (auto-update if empty) |
| `laravel-dev presets:update` | Force update presets |
| `laravel-dev config` | List configuration |
| `laravel-dev config:edit` | Interactive config editor |
| `laravel-dev preset:list` | **REMOVED** |

---

## Task 1: Add Laravel Prompts Dependency

**Files:**
- Modify: `composer.json`

**Step 1: Add laravel/prompts to composer.json**

Add to `require` section:
```json
"laravel/prompts": "^0.3.0"
```

**Step 2: Install dependency**

Run: `composer update`
Expected: Composer downloads laravel/prompts

**Step 3: Commit**

```bash
git add composer.json composer.lock
git commit -m "feat: add laravel/prompts dependency for interactive CLI"
```

---

## Task 2: Create PresetConfig Service

**Files:**
- Create: `src/Services/PresetConfig.php`
- Create: `tests/Unit/Services/PresetConfigTest.php`

**Step 1: Write the failing test**

```php
<?php

namespace XMultibyte\LaravelDev\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use XMultibyte\LaravelDev\Services\PresetConfig;

class PresetConfigTest extends TestCase
{
    private string $testConfigDir;
    private PresetConfig $config;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->testConfigDir = sys_get_temp_dir() . '/laravel-dev-test-' . uniqid();
        mkdir($this->testConfigDir . '/config', 0755, true);
        $this->config = new PresetConfig($this->testConfigDir);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        if (is_dir($this->testConfigDir)) {
            array_map('unlink', glob($this->testConfigDir . '/config/*'));
            rmdir($this->testConfigDir . '/config');
            rmdir($this->testConfigDir);
        }
    }
    
    public function test_get_default_repository(): void
    {
        $this->assertEquals(
            'https://github.com/x-multibyte/laravel-dev-presets.git',
            $this->config->get('repository')
        );
    }
    
    public function test_get_default_branch(): void
    {
        $this->assertEquals('main', $this->config->get('branch'));
    }
    
    public function test_get_with_env_override(): void
    {
        putenv('LARAVEL_DEV_PRESETS_REPO=https://github.com/custom/presets.git');
        
        $this->assertEquals(
            'https://github.com/custom/presets.git',
            $this->config->get('repository')
        );
        
        putenv('LARAVEL_DEV_PRESETS_REPO');
    }
    
    public function test_set_and_save(): void
    {
        $this->config->set('branch', 'develop');
        $this->config->save();
        
        $this->assertEquals('develop', $this->config->get('branch'));
        
        // Verify file was created
        $configFile = $this->testConfigDir . '/config/presets.json';
        $this->assertFileExists($configFile);
    }
    
    public function test_all_returns_all_config(): void
    {
        $all = $this->config->all();
        
        $this->assertArrayHasKey('repository', $all);
        $this->assertArrayHasKey('branch', $all);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Unit/Services/PresetConfigTest.php`
Expected: FAIL with "Class not found"

**Step 3: Write the implementation**

```php
<?php

namespace XMultibyte\LaravelDev\Services;

use XMultibyte\LaravelDev\Support\Filesystem;

class PresetConfig
{
    private const DEFAULTS = [
        'repository' => 'https://github.com/x-multibyte/laravel-dev-presets.git',
        'branch' => 'main',
        'auto_update' => true,
    ];
    
    private const ENV_MAPPING = [
        'repository' => 'LARAVEL_DEV_PRESETS_REPO',
        'branch' => 'LARAVEL_DEV_PRESETS_BRANCH',
    ];
    
    private Filesystem $fs;
    private string $configDir;
    private array $config = [];
    private bool $loaded = false;
    
    public function __construct(?string $homeDir = null)
    {
        $this->fs = new Filesystem();
        
        $home = $homeDir ?? $this->fs->expandHomePath('~/.laravel-dev');
        $this->configDir = $home . '/config';
    }
    
    public function get(string $key, mixed $default = null): mixed
    {
        $this->load();
        
        // Check environment variable first
        if (isset(self::ENV_MAPPING[$key])) {
            $envValue = getenv(self::ENV_MAPPING[$key]);
            if ($envValue !== false && $envValue !== '') {
                return $envValue;
            }
        }
        
        return $this->config[$key] ?? self::DEFAULTS[$key] ?? $default;
    }
    
    public function set(string $key, mixed $value): void
    {
        $this->load();
        $this->config[$key] = $value;
    }
    
    public function all(): array
    {
        $this->load();
        return array_merge(self::DEFAULTS, $this->config);
    }
    
    public function save(): void
    {
        $this->fs->ensureDirectoryExists($this->configDir);
        
        $data = array_merge(self::DEFAULTS, $this->config);
        $data['last_updated'] = date('c');
        
        $this->fs->write(
            $this->configDir . '/presets.json',
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
    
    public function reset(): void
    {
        $this->config = [];
        $this->loaded = false;
    }
    
    private function load(): void
    {
        if ($this->loaded) {
            return;
        }
        
        $configFile = $this->configDir . '/presets.json';
        
        if ($this->fs->exists($configFile)) {
            $content = $this->fs->read($configFile);
            $data = json_decode($content, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $this->config = $data;
            }
        }
        
        $this->loaded = true;
    }
}
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Unit/Services/PresetConfigTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Services/PresetConfig.php tests/Unit/Services/PresetConfigTest.php
git commit -m "feat: add PresetConfig service for configuration management"
```

---

## Task 3: Create PresetGitService

**Files:**
- Create: `src/Services/PresetGitService.php`
- Create: `tests/Unit/Services/PresetGitServiceTest.php`

**Step 1: Write the failing test**

```php
<?php

namespace XMultibyte\LaravelDev\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use XMultibyte\LaravelDev\Services\PresetGitService;

class PresetGitServiceTest extends TestCase
{
    private string $testDir;
    private PresetGitService $gitService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = sys_get_temp_dir() . '/laravel-dev-git-test-' . uniqid();
        mkdir($this->testDir, 0755, true);
        $this->gitService = new PresetGitService();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->removeDirectory($this->testDir);
    }
    
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
    
    public function test_is_git_repo_returns_false_for_non_git_dir(): void
    {
        $this->assertFalse($this->gitService->isGitRepo($this->testDir));
    }
    
    public function test_clone_creates_git_repo(): void
    {
        // Use a small public repo for testing
        $repo = 'https://github.com/octocat/Hello-World.git';
        
        $this->gitService->clone($repo, $this->testDir);
        
        $this->assertTrue($this->gitService->isGitRepo($this->testDir));
        $this->assertFileExists($this->testDir . '/README');
    }
    
    public function test_clone_throws_exception_for_invalid_repo(): void
    {
        $this->expectException(\RuntimeException::class);
        
        $this->gitService->clone('https://invalid-url-not-exist.git', $this->testDir);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Unit/Services/PresetGitServiceTest.php`
Expected: FAIL with "Class not found"

**Step 3: Write the implementation**

```php
<?php

namespace XMultibyte\LaravelDev\Services;

use Symfony\Component\Process\Process;
use RuntimeException;

class PresetGitService
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY = 1;
    
    public function clone(string $repo, string $target): void
    {
        $this->executeWithRetry(function () use ($repo, $target) {
            $process = new Process(['git', 'clone', $repo, $target]);
            $process->setTimeout(300);
            $process->run();
            
            if (!$process->isSuccessful()) {
                throw new RuntimeException(
                    'Failed to clone repository: ' . $process->getErrorOutput()
                );
            }
        });
    }
    
    public function update(string $target, string $branch = 'main'): void
    {
        if (!$this->isGitRepo($target)) {
            throw new RuntimeException("Not a git repository: {$target}");
        }
        
        $this->executeWithRetry(function () use ($target, $branch) {
            // Fetch all
            $fetch = new Process(['git', 'fetch', '--all']);
            $fetch->setWorkingDirectory($target);
            $fetch->run();
            
            if (!$fetch->isSuccessful()) {
                throw new RuntimeException('Failed to fetch: ' . $fetch->getErrorOutput());
            }
            
            // Reset to origin/main
            $reset = new Process(['git', 'reset', '--hard', "origin/{$branch}"]);
            $reset->setWorkingDirectory($target);
            $reset->run();
            
            if (!$reset->isSuccessful()) {
                throw new RuntimeException('Failed to reset: ' . $reset->getErrorOutput());
            }
            
            // Clean untracked files
            $clean = new Process(['git', 'clean', '-fd']);
            $clean->setWorkingDirectory($target);
            $clean->run();
        });
    }
    
    public function isGitRepo(string $path): bool
    {
        return is_dir($path . '/.git');
    }
    
    private function executeWithRetry(callable $operation): void
    {
        $attempts = 0;
        $lastException = null;
        
        while ($attempts < self::MAX_RETRIES) {
            try {
                $operation();
                return;
            } catch (RuntimeException $e) {
                $lastException = $e;
                $attempts++;
                
                if ($attempts < self::MAX_RETRIES) {
                    sleep(self::RETRY_DELAY);
                }
            }
        }
        
        throw new RuntimeException(
            "Failed after {$attempts} attempts: " . $lastException?->getMessage()
        );
    }
}
```

**Step 4: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Unit/Services/PresetGitServiceTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Services/PresetGitService.php tests/Unit/Services/PresetGitServiceTest.php
git commit -m "feat: add PresetGitService for Git operations using Symfony Process"
```

---

## Task 4: Refactor PresetService

**Files:**
- Modify: `src/Services/PresetService.php`
- Modify: `tests/Unit/Services/PresetServiceTest.php`

**Step 1: Write the failing test for new methods**

Add to `tests/Unit/Services/PresetServiceTest.php`:

```php
public function test_is_installed_returns_false_when_empty(): void
{
    $service = new PresetService($this->testDir);
    $this->assertFalse($service->isInstalled());
}

public function test_ensure_updated_creates_directory(): void
{
    $service = new PresetService($this->testDir);
    $service->ensureUpdated();
    
    $this->assertTrue($service->isInstalled());
}
```

**Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Unit/Services/PresetServiceTest.php`
Expected: FAIL with "Method isInstalled does not exist"

**Step 3: Update PresetService implementation**

```php
<?php

namespace XMultibyte\LaravelDev\Services;

use XMultibyte\LaravelDev\Domain\Preset;
use XMultibyte\LaravelDev\Support\Filesystem;

class PresetService
{
    public const DEFAULT_CACHE_PATH = '~/.laravel-dev/presets';
    
    private string $cachePath;
    private Filesystem $fs;
    private PresetConfig $config;
    private PresetGitService $git;
    
    public function __construct(?string $cachePath = null)
    {
        $this->fs = new Filesystem();
        $this->cachePath = $this->fs->expandHomePath($cachePath ?? self::DEFAULT_CACHE_PATH);
        $this->config = new PresetConfig();
        $this->git = new PresetGitService();
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
    
    public function isInstalled(): bool
    {
        return is_dir($this->cachePath) && $this->git->isGitRepo($this->cachePath);
    }
    
    public function ensureUpdated(): void
    {
        if ($this->isInstalled()) {
            $this->git->update(
                $this->cachePath,
                $this->config->get('branch')
            );
        } else {
            $this->fs->ensureDirectoryExists(dirname($this->cachePath));
            $this->git->clone(
                $this->config->get('repository'),
                $this->cachePath
            );
        }
        
        $this->config->set('last_updated', date('c'));
        $this->config->save();
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

Run: `./vendor/bin/phpunit tests/Unit/Services/PresetServiceTest.php`
Expected: PASS

**Step 5: Commit**

```bash
git add src/Services/PresetService.php tests/Unit/Services/PresetServiceTest.php
git commit -m "refactor: integrate Git and config management into PresetService"
```

---

## Task 5: Create PresetsCommand

**Files:**
- Create: `src/Console/Commands/PresetsCommand.php`

**Step 1: Write the implementation**

```php
<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use XMultibyte\LaravelDev\Services\PresetService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\spin;

#[AsCommand(
    name: 'presets',
    description: 'List all available presets'
)]
class PresetsCommand extends Command
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
        
        // Auto-update if not installed
        if (!$service->isInstalled()) {
            $update = confirm(
                label: 'Presets not found. Download now?',
                default: true,
                yes: 'Yes',
                no: 'No'
            );
            
            if ($update) {
                spin(
                    callback: fn() => $service->ensureUpdated(),
                    message: 'Downloading presets...'
                );
                info('Presets downloaded successfully!');
            } else {
                warning('Operation cancelled. Run `laravel-dev presets:update` to download later.');
                return Command::SUCCESS;
            }
        }
        
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
            warning('No presets found matching the criteria.');
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

**Step 2: Commit**

```bash
git add src/Console/Commands/PresetsCommand.php
git commit -m "feat: add PresetsCommand with Laravel Prompts integration"
```

---

## Task 6: Create PresetsUpdateCommand

**Files:**
- Create: `src/Console/Commands/PresetsUpdateCommand.php`

**Step 1: Write the implementation**

```php
<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use XMultibyte\LaravelDev\Services\PresetService;
use XMultibyte\LaravelDev\Support\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\spin;

#[AsCommand(
    name: 'presets:update',
    description: 'Update presets from Git repository'
)]
class PresetsUpdateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force re-clone (delete existing)');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $service = new PresetService();
        $fs = new Filesystem();
        $force = $input->getOption('force');
        $presetPath = $service->getCachePath();
        
        if ($force && $fs->exists($presetPath)) {
            $confirm = confirm(
                label: 'Force update will delete existing presets. Continue?',
                default: false
            );
            
            if (!$confirm) {
                warning('Operation cancelled.');
                return Command::SUCCESS;
            }
            
            $fs->deleteDirectory($presetPath);
        }
        
        spin(
            callback: fn() => $service->ensureUpdated(),
            message: $service->isInstalled() ? 'Updating presets...' : 'Cloning presets...'
        );
        
        info('Presets updated successfully!');
        
        return Command::SUCCESS;
    }
}
```

**Step 2: Commit**

```bash
git add src/Console/Commands/PresetsUpdateCommand.php
git commit -m "feat: add PresetsUpdateCommand for Git-based preset updates"
```

---

## Task 7: Create ConfigCommand

**Files:**
- Create: `src/Console/Commands/ConfigCommand.php`

**Step 1: Write the implementation**

```php
<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use XMultibyte\LaravelDev\Services\PresetConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\info;

#[AsCommand(
    name: 'config',
    description: 'List all configuration values'
)]
class ConfigCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = new PresetConfig();
        
        info('Current configuration:');
        
        $all = $config->all();
        
        foreach ($all as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $output->writeln("  <info>{$key}:</info> {$value}");
        }
        
        return Command::SUCCESS;
    }
}
```

**Step 2: Commit**

```bash
git add src/Console/Commands/ConfigCommand.php
git commit -m "feat: add ConfigCommand to display configuration"
```

---

## Task 8: Create ConfigEditCommand

**Files:**
- Create: `src/Console/Commands/ConfigEditCommand.php`

**Step 1: Write the implementation**

```php
<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use XMultibyte\LaravelDev\Services\PresetConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;

#[AsCommand(
    name: 'config:edit',
    description: 'Interactive configuration editor'
)]
class ConfigEditCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = new PresetConfig();
        
        info('Interactive Configuration Editor');
        
        $configKey = select(
            label: 'Select configuration to edit',
            options: [
                'repository' => 'Preset repository URL',
                'branch' => 'Git branch',
                'auto_update' => 'Auto update',
            ]
        );
        
        $currentValue = $config->get($configKey);
        
        $newValue = match($configKey) {
            'repository' => text(
                label: 'Enter preset repository URL',
                default: $currentValue,
                placeholder: 'https://github.com/x-multibyte/laravel-dev-presets.git'
            ),
            
            'branch' => text(
                label: 'Enter Git branch name',
                default: $currentValue,
                placeholder: 'main'
            ),
            
            'auto_update' => confirm(
                label: 'Enable auto update?',
                default: (bool) $currentValue
            ),
            
            default => text(
                label: "Enter value for {$configKey}",
                default: (string) $currentValue
            )
        };
        
        $config->set($configKey, $newValue);
        $config->save();
        
        $displayValue = is_bool($newValue) ? ($newValue ? 'true' : 'false') : $newValue;
        info("Configuration updated: {$configKey} = {$displayValue}");
        
        if (confirm('Continue editing?', default: false)) {
            return $this->execute($input, $output);
        }
        
        return Command::SUCCESS;
    }
}
```

**Step 2: Commit**

```bash
git add src/Console/Commands/ConfigEditCommand.php
git commit -m "feat: add ConfigEditCommand with interactive configuration editing"
```

---

## Task 9: Remove PresetListCommand

**Files:**
- Delete: `src/Console/Commands/PresetListCommand.php`
- Delete: `tests/Unit/Console/Commands/PresetListCommandTest.php` (if exists)

**Step 1: Delete the file**

```bash
rm src/Console/Commands/PresetListCommand.php
```

**Step 2: Commit**

```bash
git add -A
git commit -m "refactor: remove deprecated PresetListCommand (replaced by PresetsCommand)"
```

---

## Task 10: Update Application.php

**Files:**
- Modify: `src/Console/Application.php`

**Step 1: Update command registration**

Replace `PresetListCommand` with new commands:

```php
<?php

namespace XMultibyte\LaravelDev\Console;

use XMultibyte\LaravelDev\Console\Commands\ConfigCommand;
use XMultibyte\LaravelDev\Console\Commands\ConfigEditCommand;
use XMultibyte\LaravelDev\Console\Commands\DocsCommand;
use XMultibyte\LaravelDev\Console\Commands\NewCommand;
use XMultibyte\LaravelDev\Console\Commands\PresetsCommand;
use XMultibyte\LaravelDev\Console\Commands\PresetsUpdateCommand;
use XMultibyte\LaravelDev\Console\Commands\SkillCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('laravel-dev', '1.0.5');
        
        $this->registerCommands();
    }
    
    private function registerCommands(): void
    {
        $this->addCommand(new SkillCommand());
        $this->addCommand(new PresetsCommand());
        $this->addCommand(new PresetsUpdateCommand());
        $this->addCommand(new ConfigCommand());
        $this->addCommand(new ConfigEditCommand());
        $this->addCommand(new NewCommand());
        $this->addCommand(new DocsCommand());
    }
}
```

**Step 2: Commit**

```bash
git add src/Console/Application.php
git commit -m "refactor: update Application to register new commands"
```

---

## Task 11: Run All Tests

**Step 1: Run full test suite**

Run: `./vendor/bin/phpunit`
Expected: All tests PASS

**Step 2: Fix any failures**

If any tests fail, fix them before proceeding.

**Step 3: Commit (if fixes needed)**

```bash
git add -A
git commit -m "fix: resolve test failures"
```

---

## Task 12: Manual Integration Test

**Step 1: Test presets command**

Run: `php bin/laravel-dev presets`
Expected: Prompts to download if not installed, then lists presets

**Step 2: Test presets:update command**

Run: `php bin/laravel-dev presets:update`
Expected: Updates presets from Git

**Step 3: Test config command**

Run: `php bin/laravel-dev config`
Expected: Lists all configuration values

**Step 4: Test config:edit command**

Run: `php bin/laravel-dev config:edit`
Expected: Interactive editor opens

---

## Summary

After completing all tasks:

- ✅ Laravel Prompts integrated
- ✅ Git-based preset management implemented
- ✅ Configuration management added
- ✅ Interactive CLI with modern UX
- ✅ Deprecated command removed
- ✅ All tests passing

**Commands available:**
- `laravel-dev presets` - List presets
- `laravel-dev presets:update` - Update presets
- `laravel-dev config` - View config
- `laravel-dev config:edit` - Edit config

**Run `php bin/laravel-dev list` to see all commands.**
