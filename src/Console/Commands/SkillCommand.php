<?php

namespace XMultibyte\LaravelDev\Console\Commands;

use XMultibyte\LaravelDev\Domain\AIPlatform;
use XMultibyte\LaravelDev\Services\AIDetector;
use XMultibyte\LaravelDev\Services\SkillInstaller;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(
    name: 'skill',
    description: 'Install Laravel Dev SKILL to AI coding assistant'
)]
class SkillCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('ai', 'a', InputOption::VALUE_OPTIONAL, 'Target AI platform')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing SKILL')
            ->addOption('offline', null, InputOption::VALUE_NONE, 'Use local cache')
            ->addOption('no-presets', null, InputOption::VALUE_NONE, 'Skip preset sync');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectPath = getcwd();
        $detector = new AIDetector();
        $installer = new SkillInstaller();
        
        // Detect installed platforms
        $detected = $detector->detect($projectPath);
        
        // Get target platform
        $aiType = $input->getOption('ai');
        
        if ($aiType) {
            $platform = AIPlatform::tryFrom($aiType);
            if (!$platform) {
                $output->writeln("<error>Invalid AI platform: {$aiType}</error>");
                return Command::FAILURE;
            }
        } else {
            // Interactive selection
            $platforms = AIPlatform::all();
            $choices = array_map(
                fn(AIPlatform $p) => $p->getDisplayName() . (in_array($p, $detected) ? ' (detected)' : ''),
                $platforms
            );
            $choices[] = AIPlatform::ALL->getDisplayName();
            
            $question = new ChoiceQuestion(
                'Select AI assistant to install for:',
                $choices,
                0
            );
            
            $helper = $this->getHelper('question');
            $selected = $helper->ask($input, $output, $question);
            $selectedIndex = array_search($selected, $choices);
            $platform = $selectedIndex < count($platforms) 
                ? $platforms[$selectedIndex] 
                : AIPlatform::ALL;
        }
        
        $output->writeln("<info>Installing for: {$platform->getDisplayName()}</info>");
        
        try {
            $force = $input->getOption('force');
            $offline = $input->getOption('offline');
            
            if ($platform === AIPlatform::ALL) {
                $platforms = $detector->getSupportedPlatforms();
                foreach ($platforms as $p) {
                    $installer->install($p, $projectPath, $force, $offline);
                }
            } else {
                $installer->install($platform, $projectPath, $force, $offline);
            }
            
            $output->writeln('');
            $output->writeln('<info>✓ SKILL installed successfully!</info>');
            $output->writeln('');
            $output->writeln('<comment>Next steps:</comment>');
            $output->writeln('  1. Restart your AI coding assistant');
            $output->writeln('  2. Try: "Create a Laravel 12 API project"');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}