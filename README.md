# Laravel Dev CLI

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Composer](https://img.shields.io/badge/Composer-v2-green)](https://getcomposer.org)

A Composer global CLI tool for Laravel development with AI Agent integration.

## Features

- **AI Platform Support** - Install Laravel Dev SKILL to 16+ AI coding assistants (Claude, Cursor, Windsurf, Copilot, etc.)
- **Preset Management** - Create Laravel projects with pre-configured presets
- **Documentation Query** - Quick access to Laravel documentation
- **Smart Detection** - Automatically detects installed AI platforms

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
laravel-dev preset:list

# Create a new Laravel project with preset
laravel-dev new my-project

# Install Laravel Dev SKILL to your AI assistant
laravel-dev skill

# Query Laravel documentation
laravel-dev docs routing
```

## Available Commands

| Command       | Description                                      |
|---------------|--------------------------------------------------|
| `skill`       | Install Laravel Dev SKILL to AI coding assistant |
| `preset:list` | List all available presets                       |
| `new`         | Create a new Laravel project with preset         |
| `docs`        | Query Laravel documentation                      |

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

## License

MIT License - see [LICENSE](LICENSE) file for details.