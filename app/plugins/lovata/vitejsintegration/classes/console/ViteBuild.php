<?php
declare(strict_types=1);

namespace Lovata\ViteJsintegration\Classes\Console;

use Cms\Classes\Theme;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class ViteBuild extends Command implements Isolatable
{
    public $signature = 'vite:build {theme?} {--mode=development : Mode build} {--clean : Remove node modules directory}';
    public $description = 'Build template assets';

    /**
     * @throws \SystemException
     */
    public function handle()
    {
        $theme = Theme::load($this->argument('theme') ?: Theme::getActiveThemeCode());
        $process = new Process(['npm', 'run', 'build', '--mode='. $this->option('mode')], $theme->getPath());

        try {
            $process->mustRun();
            $this->info($process->getOutput());

            if ($this->option('clean')) {
                File::deleteDirectory($theme->getPath() .'/node_modules');
            }
        } catch (ProcessFailedException $exception) {
            $this->error($exception->getMessage());
        }
    }
}
