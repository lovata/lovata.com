<?php namespace Lovata\FeedbackForCrm;

use Event;
use System\Classes\PluginBase;
// Console.
use Lovata\FeedbackForCrm\Classes\Console\RefreshTokenRefreshToken;
// Settings event.
use Lovata\FeedbackForCrm\Classes\Event\Settings\AmoCrmSettingsModelHandler;
// Record event.
use Lovata\FeedbackForCrm\Classes\Event\Record\RecordModelHandler;

/**
 * Class Plugin
 * @package Lovata\FeedbackForCrm
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    /** @var array Plugin dependencies */
    public $require = [
        'Lovata.Toolbox',
        'October.Drivers',
    ];

    /**
     * Plugin register method.
     */
    public function register()
    {
        $this->registerConsoleCommand('feedback_for_crm:amo_crm_refresh_token', RefreshTokenRefreshToken::class);
    }

    /**
     * Plugin boot method.
     * @return void
     */
    public function boot()
    {
        // Settings event.
        Event::subscribe(AmoCrmSettingsModelHandler::class);
        // Record event.
        Event::subscribe(RecordModelHandler::class);
    }

    /**
     * Plugin register settings method.
     * @return array
     */
    public function registerSettings()
    {
        return [
            'amo_crm_config' => [
                'label'       => 'lovata.feedbackforcrm::lang.menu.amo_crm',
                'icon'        => 'icon-cogs',
                'description' => '',
                'class'       => 'Lovata\FeedbackForCrm\Models\AmoCrmSettings',
                'order'       => 100,
                'permissions' => [
                    'feedback_for_crm',
                ],
            ]
        ];
    }
}
