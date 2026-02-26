---
layout: home

hero:
  name: Laravel Dev CLI
  text: CLI tool for Laravel development with AI Agent integration
  tagline: Supercharge your Laravel development workflow
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: View on GitHub
      link: https://github.com/x-multibyte/laravel-dev-cli

features:
  - title: AI Platform Support
    details: Install Laravel Dev SKILL to 16+ AI coding assistants including Claude, Cursor, Windsurf, Copilot, and more.
  - title: Preset Management
    details: Create Laravel projects with pre-configured presets for rapid development.
  - title: Documentation Query
    details: Quick access to Laravel documentation directly from your terminal.
  - title: Smart Detection
    details: Automatically detects and works with your installed AI platforms.
---

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