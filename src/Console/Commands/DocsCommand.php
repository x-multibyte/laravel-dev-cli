<?php

namespace LaravelDev\Console\Commands;

use LaravelDev\Services\DocsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'docs',
    description: 'Query Laravel documentation'
)]
class DocsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('topic', InputArgument::OPTIONAL, 'Documentation topic')
            ->addOption('laravel', 'l', InputOption::VALUE_OPTIONAL, 'Laravel version', '12')
            ->addOption('list', null, InputOption::VALUE_NONE, 'List available topics');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $service = new DocsService();
        
        if ($input->getOption('list')) {
            $output->writeln('<info>Available topics:</info>');
            foreach ($service->listTopics() as $topic) {
                $output->writeln("  - {$topic}");
            }
            $output->writeln('');
            $output->writeln('<info>Available versions:</info>');
            foreach ($service->getVersions() as $version) {
                $output->writeln("  - {$version}");
            }
            return Command::SUCCESS;
        }
        
        $topic = $input->getArgument('topic');
        
        if (!$topic) {
            $output->writeln('<error>Please specify a topic or use --list</error>');
            return Command::FAILURE;
        }
        
        $version = $input->getOption('laravel');
        $content = $service->get($topic, $version);
        
        if (!$content) {
            $output->writeln("<error>Topic not found: {$topic} (version {$version})</error>");
            return Command::FAILURE;
        }
        
        $output->writeln($content);
        
        return Command::SUCCESS;
    }
}