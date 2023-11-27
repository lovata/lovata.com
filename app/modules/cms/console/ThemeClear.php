<?php namespace Cms\Console;

use System\Helpers\Cache as CacheHelper;
use Illuminate\Console\Command;

/**
 * ThemeClear caches the system themes
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class ThemeClear extends Command
{
     /**
     * @var string name of console command
     */
    protected $name = 'theme:clear';

    /**
     * @var string description of the console command
     */
    protected $description = 'Remove theme cache files.';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        CacheHelper::instance()->clearThemeCache();

        $this->components->info('Theme cache cleared successfully.');
    }
}
