# Configuration

Learn how to configure Laravel Dev CLI for your workflow.

## Configuration Files

Laravel Dev CLI looks for configuration in the following locations (in order):

1. `~/.config/laravel-dev/config.json`
2. `~/.laravel-dev/config.json`
3. `~/.laravel-dev.json`

## Configuration Options

### Default Preset

Set the default preset for new projects:

```json
{
  "defaultPreset": "api"
}
```

### Default Laravel Version

Set the default Laravel version:

```json
{
  "defaultLaravelVersion": "11.0"
}
```

### AI Platform Detection

Configure AI platform detection:

```json
{
  "aiPlatforms": {
    "autoDetect": true,
    "enabled": ["claude", "cursor", "copilot"]
  }
}
```

### Documentation Cache

Configure documentation caching:

```json
{
  "docs": {
    "cacheEnabled": true,
    "cacheDir": "~/.cache/laravel-dev/docs",
    "defaultVersion": "11"
  }
}
```

### Preset Sources

Configure preset sources:

```json
{
  "presets": {
    "sources": [
      "vendor/x-multibyte/laravel-dev-cli/resources/presets",
      "~/.config/laravel-dev/presets"
    ]
  }
}
```

## Environment Variables

Override configuration with environment variables:

| Variable | Description |
|----------|-------------|
| `LARAVEL_DEV_CONFIG` | Path to custom config file |
| `LARAVEL_DEV_HOME` | Custom home directory |
| `LARAVEL_DEV_PRESETS_DIR` | Custom presets directory |
| `LARAVEL_DEV_CACHE_DIR` | Custom cache directory |
| `LARAVEL_DEV_OFFLINE` | Enable offline mode (1 or 0) |
| `LARAVEL_DEV_VERBOSE` | Enable verbose output (1 or 0) |

## Example Configuration

```json
{
  "defaultPreset": "api",
  "defaultLaravelVersion": "11.0",
  "aiPlatforms": {
    "autoDetect": true,
    "enabled": ["claude", "cursor", "copilot", "windsurf"]
  },
  "docs": {
    "cacheEnabled": true,
    "cacheDir": "~/.cache/laravel-dev/docs",
    "defaultVersion": "11",
    "offlineFallback": true
  },
  "presets": {
    "sources": [
      "vendor/x-multibyte/laravel-dev-cli/resources/presets",
      "~/.config/laravel-dev/presets"
    ]
  },
  "network": {
    "timeout": 30,
    "retries": 3
  },
  "logging": {
    "level": "info",
    "file": "~/.laravel-dev/logs/laravel-dev.log"
  }
}
```

## Command Line Overrides

Most configuration options can be overridden via command line:

```bash
# Override default preset
laravel-dev new my-app --preset=full-stack

# Override default version
laravel-dev new my-app --version=10

# Override cache setting
laravel-dev docs routing --no-cache
```

## Validation

Configuration is validated on load. Invalid configurations will show warnings but won't prevent operation.

## Related

- [Commands Reference](/reference/commands)
- [Getting Started](/guide/getting-started)