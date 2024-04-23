<?php namespace System\Console;

use Illuminate\Console\Command;
use System\Helpers\Cache as CacheHelper;
use System\Classes\UpdateManager;
use October\Rain\Composer\Manager as ComposerManager;
use Exception;

/**
 * OctoberUpdate performs a system update.
 *
 * This updates October CMS and all plugins, database and libraries.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class OctoberUpdate extends Command
{
    /**
     * @var string name of console command
     */
    protected $name = 'october:update';

    /**
     * @var string description of the console command
     */
    protected $description = 'Updates October CMS and all plugins, database and files.';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $composer = ComposerManager::instance();
        $composer->setOutputCommand($this, $this->input);

        if (!UpdateManager::instance()->canUpdateProject()) {
            $this->output->error(__("License is unpaid or has expired. Please visit octobercms.com to obtain a license."));
            exit(1);
        }

        $this->output->section(__('Updating package manager'));
        try {
            $composer->update(['composer/composer']);
        }
        catch (Exception $ex) {
        }

        $this->output->section(__('Updating application files'));
        $composer->update();

        CacheHelper::instance()->clearMeta();

        $this->output->section(__('Setting build number'));
        static::passthruArtisan('october:util set build');
        $this->newLine()->newLine();

        $this->output->section(__('Finishing update process'));
        $errCode = null;
        static::passthruArtisan('october:migrate', $errCode);
        $this->newLine();

        if ($errCode !== 0) {
            $this->output->error('Migration failed. Check output above');
            exit(1);
        }

        $this->output->success(__('Update process complete'));
    }

    /**
     * passthruArtisan
     */
    protected static function passthruArtisan($command, &$errCode = null)
    {
        passthru('"'.PHP_BINARY.'" artisan ' .$command, $errCode);
    }
}
