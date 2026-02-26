<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use XMultibyte\LaravelDev\Domain\AIPlatform;
use XMultibyte\LaravelDev\Services\PresetService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'new',
    description: 'Create a new Laravel project with preset'
)]
class NewCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Project name')
            ->addOption('preset', 'p', InputOption::VALUE_OPTIONAL, 'Preset name (e.g., api/12)')
            ->addOption('laravel', 'l', InputOption::VALUE_OPTIONAL, 'Laravel version (10, 11, 12)')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'Installation path')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing directory')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output as JSON');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectName = $input->getArgument('name');
        
        // Validate project name to prevent path traversal
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $projectName)) {
            $output->writeln("<error>Invalid project name. Use only letters, numbers, hyphens, and underscores.</error>");
            return Command::FAILURE;
        }
        
        $path = $input->getOption('path') ?? getcwd();
        $projectPath = $path . '/' . $projectName;
        
        $presetService = new PresetService();
        
        // Get preset
        $presetName = $input->getOption('preset');
        
        if (!$presetName) {
            $presets = $presetService->list(
                laravel: $input->getOption('laravel')
            );
            
            if (empty($presets)) {
                $output->writeln('<error>No presets available. Run `laravel-dev preset:sync` first.</error>');
                return Command::FAILURE;
            }
            
            $choices = array_map(fn($p) => "{$p->name} - {$p->description}", $presets);
            $question = new ChoiceQuestion('Select preset:', $choices, 0);
            
            $helper = $this->getHelper('question');
            $selected = $helper->ask($input, $output, $question);
            $selectedIndex = array_search($selected, $choices);
            $presetName = $presets[$selectedIndex]->name;
        }
        
        $preset = $presetService->get($presetName);
        
        if (!$preset) {
            $output->writeln("<error>Preset not found: {$presetName}</error>");
            return Command::FAILURE;
        }
        
        // Confirm
        $output->writeln("<info>Preset: {$preset->name}</info>");
        $output->writeln("<comment>{$preset->description}</comment>");
        
        if (!$input->getOption('json')) {
            $question = new ConfirmationQuestion('Continue? [y/N] ', false);
            $helper = $this->getHelper('question');
            
            if (!$helper->ask($input, $output, $question)) {
                $output->writeln('<comment>Cancelled.</comment>');
                return Command::SUCCESS;
            }
        }
        
        // Create project
        $output->writeln("<info>Creating Laravel project...</info>");
        
        $laravelVersion = $preset->getLaravelMajorVersion();
        $process = new Process([
            'composer', 'create-project',
            'laravel/laravel',
            $projectPath,
            "--prefer-dist",
        ]);
        
        // Only set TTY if available and not in CI/CD
        if (function_exists('posix_isatty') && posix_isatty(STDOUT)) {
            $process->setTty(true);
        }
        
        // Set timeout to prevent hanging (5 minutes)
        $process->setTimeout(300);
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            $output->writeln('<error>Failed to create project.</error>');
            return Command::FAILURE;
        }
        
        $output->writeln('');
        $output->writeln('<info>✓ Project created successfully!</info>');
        $output->writeln("<comment>Project path: {$projectPath}</comment>");
        
        return Command::SUCCESS;
    }
}