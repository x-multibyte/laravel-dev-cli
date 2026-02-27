<?php

namespace XMultibyte\LaravelDev\Console;

use Symfony\Component\Console\Application as BaseApplication;
use XMultibyte\LaravelDev\Console\Commands\ConfigCommand;
use XMultibyte\LaravelDev\Console\Commands\ConfigEditCommand;
use XMultibyte\LaravelDev\Console\Commands\DocsCommand;
use XMultibyte\LaravelDev\Console\Commands\NewCommand;
use XMultibyte\LaravelDev\Console\Commands\PresetsCommand;
use XMultibyte\LaravelDev\Console\Commands\PresetsUpdateCommand;
use XMultibyte\LaravelDev\Console\Commands\SkillCommand;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('laravel-dev', 'v1.1.1');

        $this->registerCommands();
    }

    public function getHelp(): string
    {
        $banner = <<<'BANNER'
 _                                           _         ______                
(_)                                         | |       (______)               
 _        _____   ____  _____  _   _  _____ | | _____  _     _  _____  _   _ 
| |      (____ | / ___)(____ || | | || ___ || |(_____)| |   | || ___ || | | |
| |_____ / ___ || |    / ___ | \ V / | ____|| |       | |__/ / | ____| \ V / 
|_______)\_____||_|    \_____|  \_/  |_____) \_)      |_____/  |_____)  \_/  
BANNER;

        $coloredBanner = $this->colorizeBanner($banner);

        return "\n" . $coloredBanner . "\n\n  <info>https://laravel-dev.xmultibyte.com</info>\n\n" . parent::getHelp();
    }

    private function colorizeBanner(string $banner): string
    {
        $output = '';
        $charCount = 0;
        $frequency = 0.3;

        for ($i = 0; $i < strlen($banner); $i++) {
            $char = $banner[$i];

            if ($char === "\n") {
                $output .= "\n";
                continue;
            }

            if ($char !== ' ') {
                $charCount++;
                $red = (int) (sin($frequency * $charCount + 0) * 127 + 128);
                $green = (int) (sin($frequency * $charCount + 2 * pi() / 3) * 127 + 128);
                $blue = (int) (sin($frequency * $charCount + 4 * pi() / 3) * 127 + 128);

                // Use direct ANSI escape codes to avoid Symfony tag parsing issues
                $output .= sprintf("\033[38;2;%d;%d;%dm%s\033[0m", $red, $green, $blue, $char);
            } else {
                $output .= ' ';
            }
        }

        return $output;
    }

    private function registerCommands(): void
    {
        $this->addCommand(new SkillCommand);
        $this->addCommand(new PresetsCommand);
        $this->addCommand(new PresetsUpdateCommand);
        $this->addCommand(new ConfigCommand);
        $this->addCommand(new ConfigEditCommand);
        $this->addCommand(new NewCommand);
        $this->addCommand(new DocsCommand);
    }
}
