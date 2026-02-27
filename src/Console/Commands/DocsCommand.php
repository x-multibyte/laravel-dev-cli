<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XMultibyte\LaravelDev\Services\DocsService;

#[AsCommand(
    name: 'docs',
    description: 'Search Laravel documentation using Boost API'
)]
class DocsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('query', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Search query (multiple queries allowed)')
            ->addOption('laravel', 'l', InputOption::VALUE_OPTIONAL, 'Laravel version (e.g., 10, 11, 12)', '12')
            ->addOption('package', 'p', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Package name (e.g., laravel/framework)')
            ->addOption('tokens', 't', InputOption::VALUE_OPTIONAL, 'Token limit for response', '5000')
            ->addOption('detect', 'd', InputOption::VALUE_NONE, 'Auto-detect packages from composer.json in current directory')
            ->addOption('fallback', 'f', InputOption::VALUE_NONE, 'Use local documentation files instead of API')
            ->addOption('list', null, InputOption::VALUE_NONE, 'List supported packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $service = new DocsService;

        if ($input->getOption('list')) {
            return $this->listPackages($service, $output);
        }

        $queries = $input->getArgument('query');

        if (empty($queries)) {
            $output->writeln('<error>Please provide a search query</error>');
            $output->writeln('');
            $output->writeln('<comment>Usage:</comment>');
            $output->writeln('  laravel-dev docs "eloquent relationship"');
            $output->writeln('  laravel-dev docs "routing" --laravel=11');
            $output->writeln('  laravel-dev docs "form" --package=livewire/livewire');
            $output->writeln('  laravel-dev docs "cache" --detect');
            $output->writeln('');
            $output->writeln('<comment>Options:</comment>');
            $output->writeln('  -l, --laravel=VERSION   Laravel version (10, 11, 12)');
            $output->writeln('  -p, --package=PACKAGE   Package name (can be used multiple times)');
            $output->writeln('  -t, --tokens=LIMIT      Token limit for response (default: 5000)');
            $output->writeln('  -d, --detect            Auto-detect packages from composer.json');
            $output->writeln('  -f, --fallback          Use local documentation files');
            $output->writeln('  --list                  List supported packages');

            return Command::FAILURE;
        }

        // Use fallback mode
        if ($input->getOption('fallback')) {
            return $this->useFallback($service, $queries, $input, $output);
        }

        // Build packages array
        $packages = $this->buildPackages($input, $service, $output);

        if ($packages === null) {
            return Command::FAILURE;
        }

        $tokenLimit = (int) $input->getOption('tokens');

        try {
            $result = $service->search($queries, $packages, $tokenLimit);
            $output->writeln($result);

            return Command::SUCCESS;
        } catch (RuntimeException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }
    }

    /**
     * Build packages array from input options.
     *
     * @return array<int, array{name: string, version: string}>|null
     */
    private function buildPackages(InputInterface $input, DocsService $service, OutputInterface $output): ?array
    {
        $packages = [];
        $laravelVersion = (string) $input->getOption('laravel');

        // Auto-detect packages from composer.json
        if ($input->getOption('detect')) {
            $detected = $service->detectPackages(getcwd());

            if (! empty($detected)) {
                $output->writeln('<info>Detected packages:</info>', OutputInterface::VERBOSITY_VERBOSE);

                foreach ($detected as $pkg) {
                    $output->writeln("  - {$pkg['name']}: {$pkg['version']}", OutputInterface::VERBOSITY_VERBOSE);
                }

                $packages = $detected;
            }
        }

        // Add packages from --package option
        $packageNames = $input->getOption('package');

        foreach ($packageNames as $packageName) {
            // Check if already detected
            $exists = array_filter($packages, fn ($p) => $p['name'] === $packageName);

            if (empty($exists)) {
                $packages[] = [
                    'name' => $packageName,
                    'version' => $laravelVersion.'.x',
                ];
            }
        }

        // Default to Laravel framework if no packages specified
        if (empty($packages)) {
            $packages = [
                ['name' => 'laravel/framework', 'version' => $laravelVersion.'.x'],
            ];
        }

        return $packages;
    }

    /**
     * Use local fallback documentation.
     */
    private function useFallback(
        DocsService $service,
        array $queries,
        InputInterface $input,
        OutputInterface $output
    ): int {
        $version = (string) $input->getOption('laravel');
        $results = [];

        foreach ($queries as $query) {
            $content = $service->getFallback($query, $version);

            if ($content !== null) {
                $results[] = $content;
            }
        }

        if (empty($results)) {
            $output->writeln('<error>No local documentation found for the given queries</error>');

            return Command::FAILURE;
        }

        $output->writeln(implode("\n\n---\n\n", $results));

        return Command::SUCCESS;
    }

    /**
     * List supported packages.
     */
    private function listPackages(DocsService $service, OutputInterface $output): int
    {
        $output->writeln('<info>Supported packages:</info>');

        foreach ($service->getSupportedPackages() as $package) {
            $output->writeln("  - {$package}");
        }

        $output->writeln('');
        $output->writeln('<comment>Use --detect to auto-detect packages from composer.json</comment>');

        return Command::SUCCESS;
    }
}