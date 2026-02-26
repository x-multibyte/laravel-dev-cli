<?php

namespace LaravelDev\Console;

use LaravelDev\Console\Commands\DocsCommand;
use LaravelDev\Console\Commands\NewCommand;
use LaravelDev\Console\Commands\PresetListCommand;
use LaravelDev\Console\Commands\SkillCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('laravel-dev', '1.0.2');
        
        $this->registerCommands();
    }
    
    private function registerCommands(): void
    {
        $this->addCommand(new SkillCommand());
        $this->addCommand(new PresetListCommand());
        $this->addCommand(new NewCommand());
        $this->addCommand(new DocsCommand());
    }
}