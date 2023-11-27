<?php
declare(strict_types=1);

namespace Lovata\ViteJsintegration\Classes;

use Cms\Classes\Theme;

class ConfigResolver
{
    private Theme $theme;
    private const THEME_CONFIGRATION_FILE = 'oc-vite.config.json';

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    public function hasThemeConfig(): bool
    {
        return \File::exists($this->getPath());
    }

    public function getThemeConfiguration(): array
    {
        if (!$this->hasThemeConfig()) {
            return [];
        }

        $content = \File::get($this->getPath());
        $content = \Str::replace('@', 'themes/'. $this->theme->getDirName(), $content);

        $config = \json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $config['build_path'] = 'themes/'. $this->theme->getDirName();
        return $config;
    }

    public function getPath(): string
    {
        return $this->theme->getPath() .'/'. self::THEME_CONFIGRATION_FILE;
    }
}
