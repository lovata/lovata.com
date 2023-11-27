<?php namespace Cms\Console;

use File;
use Cms\Classes\Router as CmsRouter;
use Cms\Classes\Theme as CmsTheme;
use Illuminate\Console\Command;

/**
 * ThemeCache caches the system themes
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class ThemeCache extends Command
{
     /**
     * @var string name of console command
     */
    protected $name = 'theme:cache';

    /**
     * @var string description of the console command
     */
    protected $description = 'Create theme cache files for faster registration.';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $this->callSilent('theme:clear');

        foreach (CmsTheme::all() as $theme) {
            $this->handleTheme($theme);
        }

        $this->components->info('Themes cached successfully.');
    }

    /**
     * handleTheme command
     */
    public function handleTheme(CmsTheme $theme)
    {
        $router = $this->getFreshThemeRouter($theme);

        File::put(
            $theme->getCachedThemePath(),
            $this->buildThemeCacheFile($router)
        );
    }

    /**
     * buildThemeCacheFile
     */
    protected function buildThemeCacheFile($router)
    {
        $manifest = [
            'routes' => $router->toArray()
        ];

        return '<?php return '.var_export($manifest, true).';';
    }

    /**
     * getFreshThemeRoutes
     */
    protected function getFreshThemeRouter(CmsTheme $theme)
    {
        return (new CmsRouter($theme))->build();
    }
}
