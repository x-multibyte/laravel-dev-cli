# Getting Started

Welcome to Laravel Dev CLI! This guide will help you get started with using the CLI tool.

## Installation

Install Laravel Dev CLI globally using Composer:

```bash
composer global require x-multibyte/laravel-dev-cli
```

### Verify Installation

After installation, verify that the CLI is available:

```bash
laravel-dev --version
```

If you see a version number, the installation was successful!

### PATH Configuration

Make sure `~/.composer/vendor/bin` is in your system PATH. Add this to your shell configuration file (`.bashrc`, `.zshrc`, etc.):

```bash
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

## Core Concepts

Laravel Dev CLI is designed to enhance your Laravel development workflow with AI agent integration. Here are the key concepts:

### AI Agents

The CLI integrates with popular AI coding assistants to provide intelligent development support. Supported platforms include:

- iFlow CLI
- Claude Code
- Cursor
- Windsurf
- GitHub Copilot
- And 10+ more

### Presets

Presets are pre-configured Laravel project templates that include common setups, dependencies, and configurations.

### Documentation

Quick access to Laravel documentation helps you stay productive without leaving your terminal.

## First Steps

### 1. List Available Presets

```bash
laravel-dev preset:list
```

This will show you all available presets with their descriptions and Laravel version compatibility.

### 2. Create a New Project

```bash
laravel-dev new my-awesome-app
```

The CLI will guide you through selecting a preset and creating your new Laravel project.

### 3. Install AI Agent Skill

```bash
laravel-dev skill
```

This command will install the Laravel Dev SKILL to your detected AI coding assistant.

### 4. Query Documentation

```bash
laravel-dev docs routing
```

Quickly access Laravel documentation for any topic.

## Next Steps

- Learn about [AI Platform Integration](/guide/integration)
- Explore [Preset Management](/guide/presets)
- Read the [Commands Reference](/reference/commands)