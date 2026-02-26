# Preset Management

Presets are pre-configured Laravel project templates that help you start new projects with a solid foundation.

## Listing Presets

View all available presets:

```bash
laravel-dev preset:list
```

This command displays:
- Preset name and description
- Laravel version compatibility
- Categories and tags
- Installation requirements

### Filtering Presets

Filter presets by category:

```bash
laravel-dev preset:list --category=api
```

Filter by Laravel version:

```bash
laravel-dev preset:list --version=11
```

## Available Presets

### Standard Presets

- **basic**: Minimal Laravel setup with essential packages
- **api**: Optimized for API development with Sanctum
- **spa**: Single Page Application ready with frontend scaffolding
- **full-stack**: Complete setup with admin panel and auth

### Specialized Presets

- **ecommerce**: E-commerce ready with payment integration
- **saas**: Multi-tenant SaaS application template
- **blog**: Content management focused preset
- **microservice**: Microservice architecture ready

## Creating Projects

### Basic Usage

```bash
laravel-dev new my-project
```

The CLI will prompt you to select a preset interactively.

### Direct Preset Selection

```bash
laravel-dev new my-project --preset=api
```

### Custom Configuration

Specify additional options:

```bash
laravel-dev new my-project --preset=api --version=11
```

## Creating Custom Presets

You can create your own presets by extending the default Laravel structure:

```bash
laravel-dev preset:create my-custom-preset
```

This will:
1. Create a new preset directory
2. Generate preset configuration files
3. Set up template files and resources

### Preset Structure

```
my-custom-preset/
├── preset.json          # Preset metadata
├── composer.json        # Dependencies
├── package.json         # Frontend dependencies
├── resources/           # Template resources
│   ├── views/
│   ├── assets/
│   └── config/
└── scripts/             # Installation scripts
```

## Updating Presets

Check for preset updates:

```bash
laravel-dev preset:update --check
```

Update all presets:

```bash
laravel-dev preset:update
```

## Best Practices

1. **Choose the Right Preset**: Start with the preset that matches your project type
2. **Customize After Installation**: Modify the generated project to fit your needs
3. **Keep Presets Updated**: Regularly update presets for security and features
4. **Version Compatibility**: Ensure preset matches your target Laravel version

## Next Steps

- Explore [AI Platform Integration](/guide/integration)
- Read the [Commands Reference](/reference/commands)