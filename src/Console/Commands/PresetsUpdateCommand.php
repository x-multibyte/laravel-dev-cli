<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XMultibyte\LaravelDev\Services\PresetService;
use XMultibyte\LaravelDev\Support\Filesystem;

#[AsCommand(
    name: 'presets:update',
    description: 'Update presets from Git repository'
)]
class PresetsUpdateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force re-clone (delete existing)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $service = new PresetService;
        $fs = new Filesystem;
        $force = $input->getOption('force');
        $presetPath = $service->getCachePath();

        if ($force && $fs->exists($presetPath)) {
            $confirm = confirm(
                label: 'Force update will delete existing presets. Continue?',
                default: false
            );

            if (!$confirm) {
                warning('Operation cancelled.');
                return Command::SUCCESS;
            }

            $fs->deleteDirectory($presetPath);
        }

        spin(
            callback: fn () => $service->ensureUpdated(),
            message: $service->isInstalled() ? 'Updating presets...' : 'Cloning presets...'
        );

        info('Presets updated successfully!');

        return Command::SUCCESS;
    }
}
