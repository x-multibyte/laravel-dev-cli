<?php

namespace XMultibyte\LaravelDev\Console;

use XMultibyte\LaravelDev\Console\Commands\ConfigCommand;
use XMultibyte\LaravelDev\Console\Commands\ConfigEditCommand;
use XMultibyte\LaravelDev\Console\Commands\DocsCommand;
use XMultibyte\LaravelDev\Console\Commands\NewCommand;
use XMultibyte\LaravelDev\Console\Commands\PresetsCommand;
use XMultibyte\LaravelDev\Console\Commands\PresetsUpdateCommand;
use XMultibyte\LaravelDev\Console\Commands\SkillCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('laravel-dev', '1.0.5');
        
        $this->registerCommands();
    }
    
    private function registerCommands(): void
    {
        $this->addCommand(new SkillCommand());
        $this->addCommand(new PresetsCommand());
        $this->addCommand(new PresetsUpdateCommand());
        $this->addCommand(new ConfigCommand());
        $this->addCommand(new ConfigEditCommand());
        $this->addCommand(new NewCommand());
        $this->addCommand(new DocsCommand());
    }
}
