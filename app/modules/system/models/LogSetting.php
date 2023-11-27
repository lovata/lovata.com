<?php namespace System\Models;

use System\Models\SettingModel;

/**
 * System log settings
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class LogSetting extends SettingModel
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string settingsCode
     */
    public $settingsCode = 'system_log_settings';

    /**
     * @var mixed settingsFields definitions
     */
    public $settingsFields = 'fields.yaml';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    /**
     * filterSettingItems
     */
    public static function filterSettingItems($manager)
    {
        if (!self::isConfigured()) {
            $manager->removeSettingItem('October.System', 'request_logs');
            $manager->removeSettingItem('October.Cms', 'theme_logs');
            return;
        }

        if (!self::get('log_events')) {
            $manager->removeSettingItem('October.System', 'event_logs');
        }

        if (!self::get('log_requests')) {
            $manager->removeSettingItem('October.System', 'request_logs');
        }

        if (!self::get('log_theme')) {
            $manager->removeSettingItem('October.Cms', 'theme_logs');
        }
    }

    /**
     * IinitSettingsData for this model. This only executes when the
     * model is first created or reset to default.
     */
    public function initSettingsData()
    {
        $this->log_events = true;
        $this->log_requests = false;
        $this->log_theme = false;
    }
}
