<?php

namespace XMultibyte\LaravelDev\Domain;

final class LaravelVersion
{
    private const SUPPORTED_VERSIONS = ['10', '11', '12'];

    public function __construct(
        public readonly int $major,
        public readonly ?int $minor = null
    ) {
    }

    public static function parse(string $version): self
    {
        // Validate version format (e.g., "12", "12.1")
        if (!preg_match('/^\d+(\.\d+)?$/', $version)) {
            throw new \InvalidArgumentException("Invalid version format: {$version}. Expected format: '12' or '12.1'");
        }

        $parts = explode('.', $version);
        $major = (int) $parts[0];

        if ($major < 1 || $major > 99) {
            throw new \InvalidArgumentException("Invalid major version: {$major}. Must be between 1 and 99.");
        }

        $minor = isset($parts[1]) ? (int) $parts[1] : null;

        if ($minor !== null && ($minor < 0 || $minor > 99)) {
            throw new \InvalidArgumentException("Invalid minor version: {$minor}. Must be between 0 and 99.");
        }

        return new self($major, $minor);
    }

    public function getDocsPath(): string
    {
        return 'v' . $this->major;
    }

    public static function isSupported(string $version): bool
    {
        $major = explode('.', $version)[0];
        return in_array($major, self::SUPPORTED_VERSIONS);
    }

    public static function getSupported(): array
    {
        return self::SUPPORTED_VERSIONS;
    }
}
