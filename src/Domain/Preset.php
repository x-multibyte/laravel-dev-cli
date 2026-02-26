<?php

namespace XMultibyte\LaravelDev\Domain;

final class Preset
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $description,
        public readonly string $laravel,
        public readonly array $dependencies,
        public readonly array $devDependencies,
        public readonly array $envTemplate,
        public readonly array $commands,
        public readonly array $hooks,
        public readonly array $metadata,
    ) {}
    
    public static function fromArray(array $data): self
    {
        // Validate required keys
        $requiredKeys = ['name', 'version', 'description', 'laravel'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException("Missing required key: {$key}");
            }
        }
        
        return new self(
            name: $data['name'],
            version: $data['version'],
            description: $data['description'],
            laravel: $data['laravel']['version'] ?? $data['laravel'],
            dependencies: $data['dependencies'] ?? [],
            devDependencies: $data['dev_dependencies'] ?? [],
            envTemplate: $data['env_template'] ?? [],
            commands: $data['commands'] ?? [],
            hooks: $data['hooks'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }
    
    public function getCategory(): string
    {
        return $this->metadata['category'] ?? explode('/', $this->name)[0];
    }
    
    public function getLaravelMajorVersion(): int
    {
        $constraint = $this->laravel;
        
        // Extract major version from constraint like "^12.0" or "12.*"
        if (preg_match('/(\d+)/', $constraint, $matches)) {
            return (int) $matches[1];
        }
        
        // Fallback: extract from name like "api/12"
        $parts = explode('/', $this->name);
        return (int) end($parts);
    }
}