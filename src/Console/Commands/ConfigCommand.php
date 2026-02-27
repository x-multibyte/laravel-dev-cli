<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use function Laravel\Prompts\info;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XMultibyte\LaravelDev\Services\PresetConfig;

#[AsCommand(
    name: 'config',
    description: 'List all configuration values'
)]
class ConfigCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = new PresetConfig;

        info('Current configuration:');

        $all = $config->all();

        foreach ($all as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $output->writeln("  <info>{$key}:</info> {$value}");
        }

        return Command::SUCCESS;
    }
}
