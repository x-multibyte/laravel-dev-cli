# Development Guide

This guide helps you contribute to Laravel Dev CLI or extend it for your needs.

## Project Structure

```
laravel-dev-cli/
├── bin/
│   └── laravel-dev          # CLI entry point
├── src/
│   ├── Console/
│   │   ├── Application.php  # Symfony Console app
│   │   └── Commands/        # CLI commands
│   ├── Services/            # Business logic
│   ├── Domain/              # Domain models
│   └── Support/             # Utilities
├── tests/                   # Test suite
└── pages/                   # VitePress documentation
```

## Development Setup

### Requirements

- PHP 8.2+
- Composer
- Node.js 18+ (for documentation)
- Pest PHP (testing framework)

### Installation

Clone the repository:

```bash
git clone https://github.com/x-multibyte/laravel-dev-cli.git
cd laravel-dev-cli
composer install
```

### Running Tests

Run the full test suite:

```bash
composer test
```

Run specific test files:

```bash
./vendor/bin/pest tests/Unit/Console/ApplicationTest.php
```

### Building Documentation

Build the documentation site:

```bash
cd pages
npm install
npm run build
```

Preview documentation locally:

```bash
npm run dev
```

## Architecture

### Console Layer

Built on Symfony Console, provides the CLI interface:

- `Application.php`: Main application entry point
- `Commands/`: Individual command implementations

### Service Layer

Business logic and external integrations:

- `PresetService`: Manages preset operations
- `SkillInstaller`: Handles AI agent skill installation
- `AIDetector`: Detects installed AI platforms
- `DocsService`: Documentation query handling

### Domain Layer

Core business models:

- `Preset`: Laravel preset representation
- `AIPlatform`: AI platform metadata
- `LaravelVersion`: Version compatibility

### Support Layer

Utility classes:

- `Filesystem`: File operations
- `HttpClient`: HTTP requests

## Adding Commands

Create a new command:

```bash
php bin/console make:command MyCommand
```

Register in `src/Console/Application.php`:

```php
$app->add(new MyCommand());
```

## Adding Presets

Create a new preset configuration:

1. Add to `resources/presets/`
2. Follow the preset schema
3. Update `PresetService` to include it

## Adding AI Platform Support

1. Create platform detector in `AIDetector`
2. Add skill installer logic in `SkillInstaller`
3. Update `AIPlatform` domain model
4. Add tests for the new platform

## Coding Standards

- Follow PSR-12 coding standard
- Use type hints for all methods
- Write tests for new features
- Update documentation for user-facing changes

## Submitting Changes

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add/update tests
5. Submit a pull request

## License

By contributing, you agree that your contributions will be licensed under the MIT License.