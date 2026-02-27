# Laravel Dev CLI

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Composer](https://img.shields.io/badge/Composer-v2-green)](https://getcomposer.org)

A Composer global CLI tool for Laravel development with AI Agent integration.

## Features

- **AI Platform Support** - Install Laravel Dev SKILL to 16+ AI coding assistants (Claude, Cursor, Windsurf, Copilot, etc.)
- **Preset Management** - Create Laravel projects with pre-configured presets
- **Documentation Query** - Search Laravel documentation via Boost API
- **Smart Detection** - Automatically detects installed AI platforms and composer packages

## Requirements

- PHP 8.2 or higher
- Composer

## Installation

```bash
composer global require x-multibyte/laravel-dev-cli
```

Add `~/.composer/vendor/bin` to your PATH if not already added.

## Quick Start

```bash
# List available presets
laravel-dev presets

# Create a new Laravel project with preset
laravel-dev new my-project

# Install Laravel Dev SKILL to your AI assistant
laravel-dev skill

# Search Laravel documentation
laravel-dev docs routing

# Search with package auto-detection
laravel-dev docs "eloquent relationships" --detect

# Search specific package documentation
laravel-dev docs "components" --package=livewire/livewire
```

## Available Commands

| Command           | Description                                           |
|-------------------|-------------------------------------------------------|
| `skill`           | Install Laravel Dev SKILL to AI coding assistant      |
| `presets`         | List all available presets                            |
| `presets:update`  | Update presets from remote repository                 |
| `new`             | Create a new Laravel project with preset              |
| `docs`            | Search Laravel documentation via Boost API            |
| `config`          | Display current configuration                         |
| `config:edit`     | Edit configuration interactively                      |

## Docs Command Options

| Option                    | Description                                      |
|---------------------------|--------------------------------------------------|
| `-l, --laravel=VERSION`   | Laravel version (10, 11, 12)                     |
| `-p, --package=PACKAGE`   | Package name (can be used multiple times)        |
| `-t, --tokens=LIMIT`      | Token limit for response (default: 5000)         |
| `-d, --detect`            | Auto-detect packages from composer.json          |
| `-f, --fallback`          | Use local documentation files                    |
| `--list`                  | List supported packages                          |

## Supported AI Platforms

- iFlow CLI
- Claude Code
- Cursor
- Windsurf
- Antigravity
- GitHub Copilot
- Kiro
- Codex CLI
- Roo Code
- Qoder
- Gemini CLI
- Trae
- OpenCode
- Continue
- CodeBuddy
- Droid (Factory)

## Supported Documentation Packages

- laravel/framework
- livewire/livewire
- laravel/nova
- filament/filament
- inertiajs/inertia-laravel
- pestphp/pest

## License

MIT License - see [LICENSE](LICENSE) file for details.