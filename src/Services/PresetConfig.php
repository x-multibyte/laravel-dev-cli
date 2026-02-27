<?php

namespace XMultibyte\LaravelDev\Services;

use XMultibyte\LaravelDev\Support\Filesystem;

/**
 * Service for managing preset configuration stored in ~/.laravel-dev/config/presets.json.
 *
 * Configuration values can be overridden via environment variables:
 * - LARAVEL_DEV_PRESETS_REPO: Override repository URL
 * - LARAVEL_DEV_PRESETS_BRANCH: Override branch name
 */
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
        $this->fs = new Filesystem;

        $home = $homeDir ?? $this->fs->expandHomePath('~/.laravel-dev');
        $this->configDir = $home . '/config';
    }

    /**
     * Get a configuration value.
     *
     * Priority order:
     * 1. Environment variable (if mapped)
     * 2. User-configured value
     * 3. Default value
     *
     * @param string $key Configuration key
     * @param mixed $default Default value if not found
     * @return mixed
     */
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

    /**
     * Set a configuration value (in memory, call save() to persist).
     *
     * @param string $key Configuration key
     * @param mixed $value Value to set
     */
    public function set(string $key, mixed $value): void
    {
        $this->load();
        $this->config[$key] = $value;
    }

    /**
     * Get all configuration values.
     *
     * @return array
     */
    public function all(): array
    {
        $this->load();
        return array_merge(self::DEFAULTS, $this->config);
    }

    /**
     * Save configuration to disk.
     */
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

    /**
     * Reset in-memory configuration (reload from disk on next access).
     */
    public function reset(): void
    {
        $this->config = [];
        $this->loaded = false;
    }

    /**
     * Get the configuration directory path.
     *
     * @return string
     */
    public function getConfigDir(): string
    {
        return $this->configDir;
    }

    /**
     * Get the configuration file path.
     *
     * @return string
     */
    public function getConfigFile(): string
    {
        return $this->configDir . '/presets.json';
    }

    /**
     * Load configuration from disk.
     */
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
