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
    private const BANNER = <<<'BANNER'
 _                                           _         ______                
(_)                                         | |       (______)               
 _        _____   ____  _____  _   _  _____ | | _____  _     _  _____  _   _ 
| |      (____ | / ___)(____ || | | || ___ || |(_____)| |   | || ___ || | | |
| |_____ / ___ || |    / ___ | \ V / | ____|| |       | |__/ / | ____| \ V / 
|_______)\_____||_|    \_____|  \_/  |_____) \_)      |_____/  |_____)  \_/  


BANNER;
    
    public function __construct()
    {
        parent::__construct('laravel-dev', 'v1.1.0');
        
        $this->registerCommands();
    }
    
    public function getHelp(): string
    {
        return self::BANNER . "  <info>https://laravel-dev.xmultibyte.com</info>\n\n" . parent::getHelp();
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
