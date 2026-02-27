<?php

namespace XMultibyte\LaravelDev\Console;

use XMultibyte\LaravelDev\Console\Commands\DocsCommand;
use XMultibyte\LaravelDev\Console\Commands\NewCommand;
use XMultibyte\LaravelDev\Console\Commands\PresetListCommand;
use XMultibyte\LaravelDev\Console\Commands\SkillCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('laravel-dev', '1.0.4');
        
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