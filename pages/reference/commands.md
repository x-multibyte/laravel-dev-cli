# Commands Reference

Complete reference for all Laravel Dev CLI commands.

## skill

Install Laravel Dev SKILL to AI coding assistant.

```bash
laravel-dev skill
```

### Options

- `--platform=<name>`: Specify AI platform (e.g., `claude`, `cursor`)
- `--list`: List detected AI platforms
- `--force`: Force reinstallation

### Examples

```bash
# Auto-detect and install
laravel-dev skill

# Install for specific platform
laravel-dev skill --platform=claude

# List detected platforms
laravel-dev skill --list

# Force reinstall
laravel-dev skill --force
```

---

## preset:list

List all available presets.

```bash
laravel-dev preset:list
```

### Options

- `--category=<name>`: Filter by category
- `--version=<version>`: Filter by Laravel version
- `--format=<format>`: Output format (table, json)

### Examples

```bash
# List all presets
laravel-dev preset:list

# Filter by category
laravel-dev preset:list --category=api

# Filter by Laravel version
laravel-dev preset:list --version=11

# JSON output
laravel-dev preset:list --format=json
```

---

## new

Create a new Laravel project with preset.

```bash
laravel-dev new <project-name>
```

### Options

- `--preset=<name>`: Specify preset name
- `--version=<version>`: Laravel version
- `--dev`: Use development dependencies
- `--force`: Create in non-empty directory

### Examples

```bash
# Interactive preset selection
laravel-dev new my-app

# Use specific preset
laravel-dev new my-app --preset=api

# Specify Laravel version
laravel-dev new my-app --version=11

# Force creation
laravel-dev new my-app --force
```

---

## docs

Query Laravel documentation.

```bash
laravel-dev docs <topic>
```

### Options

- `--version=<version>`: Laravel documentation version
- `--offline`: Use offline documentation cache

### Examples

```bash
# Query routing documentation
laravel-dev docs routing

# Query specific version
laravel-dev docs eloquent --version=10

# Use offline cache
laravel-dev docs migrations --offline
```

---

## Common Options

### Global Options

- `-h, --help`: Display help information
- `-V, --version`: Display version information
- `-q, --quiet`: Suppress output
- `-v, --verbose`: Increase verbosity
- `-n, --no-interaction`: Disable interactive questions

### Environment Variables

- `LARAVEL_DEV_HOME`: Custom home directory
- `LARAVEL_DEV_PRESETS_DIR`: Custom presets directory
- `LARAVEL_DEV_OFFLINE`: Enable offline mode

## Exit Codes

- `0`: Success
- `1`: General error
- `2`: Invalid usage
- `3`: Network error
- `4`: File system error
- `5`: Platform not supported

## Related

- [Configuration](/reference/configuration)
- [Getting Started](/guide/getting-started)