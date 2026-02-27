<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XMultibyte\LaravelDev\Services\PresetService;

#[AsCommand(
    name: 'presets',
    description: 'List all available presets'
)]
class PresetsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('category', 'c', InputOption::VALUE_OPTIONAL, 'Filter by category')
            ->addOption('laravel', 'l', InputOption::VALUE_OPTIONAL, 'Filter by Laravel version')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output as JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $service = new PresetService;

        // Auto-update if not installed
        if (!$service->isInstalled()) {
            $update = confirm(
                label: 'Presets not found. Download now?',
                default: true,
                yes: 'Yes',
                no: 'No'
            );

            if ($update) {
                spin(
                    callback: fn () => $service->ensureUpdated(),
                    message: 'Downloading presets...'
                );
                info('Presets downloaded successfully!');
            } else {
                warning('Operation cancelled. Run `laravel-dev presets:update` to download later.');
                return Command::SUCCESS;
            }
        }

        $presets = $service->list(
            category: $input->getOption('category'),
            laravel: $input->getOption('laravel')
        );

        if ($input->getOption('json')) {
            $data = array_map(fn ($p) => [
                'name' => $p->name,
                'version' => $p->version,
                'description' => $p->description,
                'category' => $p->getCategory(),
                'laravel' => $p->laravel,
            ], $presets);

            $output->writeln(json_encode($data, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        }

        if (empty($presets)) {
            warning('No presets found matching the criteria.');
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Name', 'Description', 'Laravel']);

        foreach ($presets as $preset) {
            $table->addRow([
                $preset->name,
                $preset->description,
                $preset->laravel,
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
