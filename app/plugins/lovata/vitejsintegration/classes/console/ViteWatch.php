<?php
declare(strict_types=1);

namespace Lovata\ViteJsintegration\Classes\Console;

use Cms\Classes\Theme;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class ViteWatch extends Command implements Isolatable
{
    public $signature = 'vite:watch {theme?}';
    public $description = 'Watch template assets';

    /**
     * @throws \SystemException
     */
    public function handle()
    {
        $theme = Theme::load($this->argument('theme') ?: Theme::getActiveThemeCode());
        $process = new Process(['npm', 'run', 'dev'], $theme->getPath(), timeout: null);
        $process->setPty(true);
        try {
            $process->start();
            $process->wait(function($type, $data) {
                $this->info($data);
            });
        } catch (ProcessFailedException $exception) {
            $this->error($exception->getMessage());
        }
    }
}