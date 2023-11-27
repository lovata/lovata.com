<?php namespace Cms\Console;

use Cms\Classes\Theme;
use Cms\Classes\ThemeManager;
use System\Classes\UpdateManager;
use System\Helpers\Cache as CacheHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Console\Command;

/**
 * ThemeRemove removes a theme.
 *
 * This completely deletes an existing theme, including all files and directories.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class ThemeRemove extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * @var string name of console command
     */
    protected $name = 'theme:remove';

    /**
     * @var string description of the console command
     */
    protected $description = 'Delete an existing theme.';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $manager = ThemeManager::instance();
        $name = $suppliedName = (string) $this->argument('name');
        $themeExists = Theme::exists($name);

        if (!$themeExists) {
            $name = (string) $manager->findDirectoryName($name);
            $themeExists = Theme::exists($name);
        }

        $this->line('Removing Theme...');

        if (!$themeExists || !$name) {
            return $this->output->error("Unable to find theme [{$suppliedName}]");
        }

        if (!$this->confirmToProceed("This will DELETE theme [$name] from the filesystem and database.")) {
            return;
        }

        // Remove via composer
        if ($composerCode = $manager->getComposerCode($name)) {
            $this->comment("Executing: composer remove {$composerCode}");
            $this->newLine();
            UpdateManager::instance()->uninstallTheme($name);
        }
        // Remove via filesystem
        else {
            $manager->deleteTheme($name);
        }

        // Clear meta cache
        CacheHelper::instance()->clearMeta();

        $this->output->success("Theme [{$name}] removed");
    }

    /**
     * getArguments get the console command arguments
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The directory name of the theme.'],
        ];
    }

    /**
     * getOptions get the console command options
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force the operation to run.'],
        ];
    }

    /**
     * getDefaultConfirmCallback specifies the default confirmation callback
     */
    protected function getDefaultConfirmCallback()
    {
        return function () {
            return true;
        };
    }
}
