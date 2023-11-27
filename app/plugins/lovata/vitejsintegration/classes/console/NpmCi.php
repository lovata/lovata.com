<?php
declare(strict_types=1);

namespace Lovata\ViteJsintegration\Classes\Console;

use Cms\Classes\Theme;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class NpmCi extends Command implements Isolatable
{
    public $signature = 'vite:ci {theme?}';
    public $description = 'Build template assets';

    /**
     * @throws \SystemException
     */
    public function handle()
    {
        $theme = Theme::load($this->argument('theme') ?: Theme::getActiveThemeCode());
        $process = new Process(['npm', 'ci'], $theme->getPath());

        try {
            $process->mustRun();
            $this->info($process->getOutput());
        } catch (ProcessFailedException $exception) {
            $this->error($exception->getMessage());
        }
    }
}
