<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XMultibyte\LaravelDev\Services\PresetConfig;

#[AsCommand(
    name: 'config:edit',
    description: 'Interactive configuration editor'
)]
class ConfigEditCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = new PresetConfig;

        info('Interactive Configuration Editor');

        $configKey = select(
            label: 'Select configuration to edit',
            options: [
                'repository' => 'Preset repository URL',
                'branch' => 'Git branch',
                'auto_update' => 'Auto update',
            ]
        );

        $currentValue = $config->get($configKey);

        $newValue = match($configKey) {
            'repository' => text(
                label: 'Enter preset repository URL',
                default: $currentValue,
                placeholder: 'https://github.com/x-multibyte/laravel-dev-presets.git'
            ),

            'branch' => text(
                label: 'Enter Git branch name',
                default: $currentValue,
                placeholder: 'main'
            ),

            'auto_update' => confirm(
                label: 'Enable auto update?',
                default: (bool) $currentValue
            ),

            default => text(
                label: "Enter value for {$configKey}",
                default: (string) $currentValue
            )
        };

        $config->set($configKey, $newValue);
        $config->save();

        $displayValue = is_bool($newValue) ? ($newValue ? 'true' : 'false') : $newValue;
        info("Configuration updated: {$configKey} = {$displayValue}");

        if (confirm('Continue editing?', default: false)) {
            return $this->execute($input, $output);
        }

        return Command::SUCCESS;
    }
}
