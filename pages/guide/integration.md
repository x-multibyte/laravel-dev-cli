# AI Platform Integration

Laravel Dev CLI seamlessly integrates with 16+ popular AI coding assistants to enhance your Laravel development experience.

## Supported Platforms

The CLI supports the following AI platforms:

| Platform | Type | Status |
|----------|------|--------|
| iFlow CLI | CLI Agent | ✅ Fully Supported |
| Claude Code | CLI Agent | ✅ Fully Supported |
| Cursor | IDE Plugin | ✅ Fully Supported |
| Windsurf | IDE Plugin | ✅ Fully Supported |
| GitHub Copilot | IDE Plugin | ✅ Fully Supported |
| Kiro | CLI Agent | ✅ Fully Supported |
| Codex CLI | CLI Agent | ✅ Fully Supported |
| Roo Code | CLI Agent | ✅ Fully Supported |
| Qoder | IDE Plugin | ✅ Fully Supported |
| Gemini CLI | CLI Agent | ✅ Fully Supported |
| Trae | IDE Plugin | ✅ Fully Supported |
| OpenCode | CLI Agent | ✅ Fully Supported |
| Continue | IDE Plugin | ✅ Fully Supported |
| CodeBuddy | CLI Agent | ✅ Fully Supported |
| Droid (Factory) | CLI Agent | ✅ Fully Supported |
| Antigravity | CLI Agent | ✅ Fully Supported |

## Installation

### Automatic Installation

The `laravel-dev skill` command automatically detects installed AI platforms and installs the Laravel Dev SKILL:

```bash
laravel-dev skill
```

### Manual Installation

For specific platforms, you can manually install the skill:

```bash
laravel-dev skill --platform=claude
```

## How It Works

The Laravel Dev SKILL provides your AI assistant with:

1. **Laravel Knowledge Base**: Comprehensive Laravel framework knowledge
2. **Project Context**: Understanding of your Laravel project structure
3. **Best Practices**: Laravel-specific coding standards and patterns
4. **Ecosystem Knowledge**: Popular Laravel packages and tools

## Platform-Specific Notes

### Claude Code

Claude Code users get full access to Laravel documentation and best practices through the skill system.

### Cursor

Cursor integration provides intelligent code suggestions for Laravel projects.

### GitHub Copilot

The skill enhances Copilot's suggestions with Laravel-specific context and patterns.

## Troubleshooting

### Platform Not Detected

If your AI platform is not automatically detected:

1. Ensure the platform is installed and accessible
2. Run `laravel-dev skill --list` to see detected platforms
3. Specify the platform manually: `laravel-dev skill --platform=<name>`

### Skill Installation Failed

If skill installation fails:

1. Check platform permissions for skill installation
2. Ensure you have write access to the platform's skill directory
3. Consult the platform's documentation for skill installation requirements

## Contributing

Want to add support for more AI platforms? Check out our [Development Guide](/guide/development).