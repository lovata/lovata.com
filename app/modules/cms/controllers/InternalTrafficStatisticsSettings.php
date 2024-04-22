<?php namespace Cms\Controllers;

use Backend\Classes\SettingsController;
use Cms\Models\TrafficStatisticsPageview;
use Cms\Classes\TrafficLogger;
use Flash;
use Lang;

/**
 * Internal Traffic Statistics settings controller
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 *
 */
class InternalTrafficStatisticsSettings extends SettingsController
{
    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['cms.internal_traffic_statistics'];

    /**
     * @var string settingsItemCode determines the settings code
     */
    public $settingsItemCode = 'internal_traffic_statistics';

    public $turboVisitControl = 'disable';

    public function __construct()
    {
        $this->addCss('/modules/system/assets/css/settings/settings.css', 'global');

        parent::__construct();
    }

    /**
     * index
     */
    public function index()
    {
        $this->pageTitle = 'cms::lang.internal_traffic_statistics.label';
        
        $enabled = TrafficLogger::isEnabled();
        $this->vars['featureEnabled'] = $enabled;

        if ($enabled) {
            $this->vars['timezone'] = TrafficLogger::getTimezone();
    
            $retention = TrafficLogger::getRetentionMonths();
            if (!strlen($retention)) {
                $retention = Lang::get('cms::lang.internal_traffic_statistics.retention_indefinite');
            }
            else {
                $retention = Lang::get('cms::lang.internal_traffic_statistics.retention_mon', ['retention'=>$retention]);
            }

            $this->vars['retention'] = $retention;
        }
    }

    public function index_onPurgeData()
    {
        TrafficStatisticsPageview::purgeAllRecords();
        Flash::success(Lang::get('cms::lang.internal_traffic_statistics.purge_success'));
    }
}
