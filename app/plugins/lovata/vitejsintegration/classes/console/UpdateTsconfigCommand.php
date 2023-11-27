<?php
declare(strict_types=1);

namespace Lovata\ViteJsintegration\Classes\Console;

use Cms\Classes\Theme;
use Illuminate\Support\Facades\File;
use Innocenzi\Vite\Commands\UpdateTsconfigCommand as UpdateTsconfigCommandBase;

final class UpdateTsconfigCommand extends UpdateTsconfigCommandBase
{
    public $signature = 'vite:tsconfig {theme?} {--force : Force the operation to run when in production}';

    protected Theme|null $theme = null;

    /**
     * @throws \JsonException
     */
    protected function createTsConfig(): void
    {
        File::put($this->getTsConfigPath(), $this->prepareConfigContent());
    }

    /**
     * @throws \SystemException
     */
    protected function getTsConfigPath(): string
    {
        return sprintf('%s/%s', $this->getTheme()->getPath(), 'tsconfig.json');
    }

    /**
     * @throws \SystemException
     */
    protected function getTheme(): Theme
    {
        if ($this->theme === null) {
            $this->theme = Theme::load($this->argument('theme') ?? Theme::getActiveThemeCode());
        }

        return $this->theme;
    }

    /**
     * @throws \JsonException
     */
    protected function prepareConfigContent(): string
    {
        return json_encode([
            'compilerOptions' => [
                'target' => 'esnext',
                'module' => 'esnext',
                'moduleResolution' => 'node',
                'strict' => true,
                'jsx' => 'preserve',
                'sourceMap' => true,
                'resolveJsonModule' => true,
                'esModuleInterop' => true,
                'lib' => ['esnext', 'dom'],
                'types' => ['vite/client'],
            ],
            'include' => ['./**/*'],
        ], JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
    }
}
