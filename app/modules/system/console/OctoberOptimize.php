<?php

namespace System\Console;

use System;
use Illuminate\Console\Command;

/**
 * OctoberOptimize optimizes the framework and platform files
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class OctoberOptimize extends Command
{
    /**
     * @var string name of console command
     */
    protected $name = 'october:optimize';

    /**
     * @var string description of the console command
     */
    protected $description = 'Cache the framework and platform files';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $this->components->info('Caching the framework and platform files');

        $commands = collect([
            'config' => fn () => $this->callSilent('config:cache') == 0,
            'routes' => fn () => $this->callSilent('route:cache') == 0,
        ]);

        if (System::hasModule('Cms')) {
            $commands->put('theme', fn () => $this->callSilent('theme:cache') == 0);
        }

        $commands->each(fn ($task, $description) => $this->components->task($description, $task));

        $this->newLine();
    }
}
