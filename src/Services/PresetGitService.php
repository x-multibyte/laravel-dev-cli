<?php

namespace XMultibyte\LaravelDev\Services;

use RuntimeException;
use Symfony\Component\Process\Process;

class PresetGitService
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY = 1;

    /**
     * Clone a Git repository to a target directory.
     */
    public function clone(string $repo, string $target): void
    {
        $this->executeWithRetry(function () use ($repo, $target) {
            $process = new Process(['git', 'clone', $repo, $target]);
            $process->setTimeout(300);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new RuntimeException(
                    'Failed to clone repository: ' . $process->getErrorOutput()
                );
            }
        });
    }

    /**
     * Update a Git repository to the latest from remote.
     */
    public function update(string $target, string $branch = 'main'): void
    {
        if (!$this->isGitRepo($target)) {
            throw new RuntimeException("Not a git repository: {$target}");
        }

        $this->executeWithRetry(function () use ($target, $branch) {
            // Fetch all
            $fetch = new Process(['git', 'fetch', '--all']);
            $fetch->setWorkingDirectory($target);
            $fetch->run();

            if (!$fetch->isSuccessful()) {
                throw new RuntimeException('Failed to fetch: ' . $fetch->getErrorOutput());
            }

            // Reset to origin/main
            $reset = new Process(['git', 'reset', '--hard', "origin/{$branch}"]);
            $reset->setWorkingDirectory($target);
            $reset->run();

            if (!$reset->isSuccessful()) {
                throw new RuntimeException('Failed to reset: ' . $reset->getErrorOutput());
            }

            // Clean untracked files
            $clean = new Process(['git', 'clean', '-fd']);
            $clean->setWorkingDirectory($target);
            $clean->run();
        });
    }

    /**
     * Check if a directory is a Git repository.
     */
    public function isGitRepo(string $path): bool
    {
        return is_dir($path . '/.git');
    }

    /**
     * Execute an operation with retry logic.
     */
    private function executeWithRetry(callable $operation): void
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts < self::MAX_RETRIES) {
            try {
                $operation();
                return;
            } catch (RuntimeException $e) {
                $lastException = $e;
                $attempts++;

                if ($attempts < self::MAX_RETRIES) {
                    sleep(self::RETRY_DELAY);
                }
            }
        }

        throw new RuntimeException(
            "Failed after {$attempts} attempts: " . $lastException?->getMessage()
        );
    }
}
