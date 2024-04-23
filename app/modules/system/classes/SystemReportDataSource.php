<?php namespace System\Classes;

use System\Classes\UpdateManager;
use Backend\Classes\Dashboard\ReportDataOrderRule;
use Backend\Classes\Dashboard\ReportDimension;
use Backend\Classes\Dashboard\ReportData;
use Backend\Classes\Dashboard\ReportDataPaginationParams;
use Backend\Classes\Dashboard\ReportFetchDataResult;
use Backend\Classes\ReportDataSourceBase;
use System\Models\RequestLog;
use System\Models\LogSetting;
use System\Models\EventLog;
use Carbon\Carbon;
use BackendAuth;
use Backend;
use System;
use Config;
use Lang;
use Db;

/**
 * SystemReportDataSource providing the system information.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class SystemReportDataSource extends ReportDataSourceBase
{
    /**
     * @var string DIMENSION_VERSION_INFORMATION
     */
    const DIMENSION_VERSION_INFORMATION = 'indicator@version_information';

    /**
     * @var string DIMENSION_SYSTEM_ISSUES
     */
    const DIMENSION_SYSTEM_ISSUES = 'indicator@system_issues';

    /**
     * @var string DIMENSION_EVENT_LOG
     */
    const DIMENSION_EVENT_LOG = 'indicator@event_log';

    /**
     * @var string DIMENSION_REQUEST_LOG
     */
    const DIMENSION_REQUEST_LOG = 'indicator@request_log';

    /**
     * __construct the data source
     */
    public function __construct()
    {
        $langPrefix = 'system::lang.dashboard.report_data_source.';

        ReportData::addIndicatorMetrics(
            $this->addCalculatedDimension(
                self::DIMENSION_VERSION_INFORMATION,
                $langPrefix . 'dimension_version_information'
            )
            ->setDefaultWidgetConfig([
                'icon'=>'ph ph-package',
                'link_text'=>Lang::get($langPrefix.'widgets.version_information.link'),
                'title' =>Lang::get($langPrefix.'widgets.version_information.title')
            ])
        );

        ReportData::addIndicatorMetrics(
            $this->addCalculatedDimension(
                self::DIMENSION_SYSTEM_ISSUES,
                $langPrefix . 'dimension_system_issues'
            )
            ->setDefaultWidgetConfig([
                'icon'=>'ph ph-gear-fine',
                'link_text'=>Lang::get($langPrefix.'widgets.system_issues.link'),
                'title' =>Lang::get($langPrefix.'widgets.system_issues.title')
            ])
        );

        ReportData::addIndicatorMetrics(
            $this->addCalculatedDimension(
                self::DIMENSION_EVENT_LOG,
                $langPrefix . 'dimension_event_log'
            )
            ->setDefaultWidgetConfig([
                'icon'=>'ph ph-bell-ringing',
                'link_text'=>Lang::get($langPrefix.'widgets.event_log.link'),
                'title' =>Lang::get($langPrefix.'widgets.event_log.title')
            ])
        );

        ReportData::addIndicatorMetrics(
            $this->addCalculatedDimension(
                self::DIMENSION_REQUEST_LOG,
                $langPrefix . 'dimension_request_log'
            )
            ->setDefaultWidgetConfig([
                'icon'=>'ph ph-signpost',
                'link_text'=>Lang::get($langPrefix.'widgets.request_log.link'),
                'title' =>Lang::get($langPrefix.'widgets.request_log.title')
            ])
        );
    }

    /**
     * onGetPopupData
     */
    protected function onGetPopupData()
    {
        return [
            'title' => Lang::get('system::lang.dashboard.report_data_source.issues_popup'),
            'content' => $this->makePartial('warnings', ['warnings' => $this->getSystemWarnings()])
        ];
    }

    /**
     * fetchData
     */
    protected function fetchData(
        ReportDimension $dimension,
        array $metrics,
        array $metricsConfiguration,
        ?Carbon $startDate,
        ?Carbon $endDate,
        ?int $startTimestamp,
        array $dimensionFilters,
        ?string $groupInterval,
        ?ReportDataOrderRule $orderRule,
        ?int $limit,
        ?ReportDataPaginationParams $paginationParams,
        bool $hideEmptyDimensionValues,
        $totalsOnly
    ): ReportFetchDataResult {
        $result = new ReportFetchDataResult();

        if ($dimension->getCode() === self::DIMENSION_VERSION_INFORMATION) {
            return $this->getVersionInformationData($dimension, $result);
        }

        if ($dimension->getCode() === self::DIMENSION_SYSTEM_ISSUES) {
            return $this->getSystemIssuesData($dimension, $result);
        }

        if ($dimension->getCode() === self::DIMENSION_EVENT_LOG) {
            return $this->getEventLogData($dimension, $result);
        }

        if ($dimension->getCode() === self::DIMENSION_REQUEST_LOG) {
            return $this->getRequestLogData($dimension, $result);
        }

        return $result;
    }

    /**
     * getVersionInformationData
     */
    protected function getVersionInformationData(ReportDimension $dimension, ReportFetchDataResult $result): ReportFetchDataResult
    {
        $updatesAvailable = UpdateManager::instance()->check() > 0;
        $iconStatus = $updatesAvailable ?
            ReportData::INDICATOR_ICON_STATUS_IMPORTANT :
            ReportData::INDICATOR_ICON_STATUS_INFO;

        $iconComplication = $updatesAvailable ?
            ReportData::INDICATOR_ICON_COMPLICATION_UP :
            null;

        $linkEnabled = BackendAuth::userHasAccess('general.backend.perform_updates');

        return $result->setRows($this->makeResultRow($dimension, [
            ReportData::METRIC_VALUE => UpdateManager::instance()->getCurrentVersion(),
            ReportData::METRIC_INDICATOR_ICON_STATUS => $iconStatus,
            ReportData::METRIC_INDICATOR_ICON_COMPLICATION => $iconComplication,
            ReportData::METRIC_LINK_ENABLED => $linkEnabled,
            ReportData::METRIC_LINK_HREF => Backend::url('system/updates')
        ]));
    }

    /**
     * getSystemIssuesData
     */
    protected function getSystemIssuesData(ReportDimension $dimension, ReportFetchDataResult $result)
    {
        $hasIssues = count($this->getSystemWarnings()) > 0;
        $value = $hasIssues ?
            Lang::get('system::lang.dashboard.report_data_source.configuration_issues') :
            Lang::get('system::lang.dashboard.report_data_source.no_issues');

        $iconStatus = $hasIssues ?
            ReportData::INDICATOR_ICON_STATUS_IMPORTANT :
            ReportData::INDICATOR_ICON_STATUS_INFO;

        $user = BackendAuth::getUser();

        return $result->setRows($this->makeResultRow($dimension, [
            ReportData::METRIC_VALUE => $value,
            ReportData::METRIC_INDICATOR_ICON_STATUS => $iconStatus,
            ReportData::METRIC_LINK_ENABLED => $user->isSuperUser() && $hasIssues,
            ReportData::METRIC_LINK_HREF => ReportData::INDICATOR_HREF_POPUP
        ]));
    }

    /**
     * getEventLogData
     */
    protected function getEventLogData(ReportDimension $dimension, ReportFetchDataResult $result)
    {
        $logEnabled = (bool)LogSetting::get('log_events', false);
        $recordCnt = $logEnabled ? EventLog::count() : 0;

        $recordCnt = $recordCnt >= 1000
            ? round($recordCnt / 1000) . 'K'
            : number_format($recordCnt, 0);

        $logHasRecords = $logEnabled ?
            $recordCnt :
            Lang::get('system::lang.dashboard.report_data_source.log_disabled');

        $iconStatus = $logEnabled ?
            ReportData::INDICATOR_ICON_STATUS_INFO :
            ReportData::INDICATOR_ICON_STATUS_DISABLED;

        return $result->setRows($this->makeResultRow($dimension, [
            ReportData::METRIC_VALUE => $logHasRecords,
            ReportData::METRIC_INDICATOR_ICON_STATUS => $iconStatus,
            ReportData::METRIC_LINK_ENABLED => BackendAuth::userHasAccess('utilities.logs'),
            ReportData::METRIC_LINK_HREF => Backend::url('system/eventlogs')
        ]));
    }

    /**
     * getRequestLogData
     */
    protected function getRequestLogData(ReportDimension $dimension, ReportFetchDataResult $result)
    {
        $logEnabled = (bool)LogSetting::get('log_requests', false);
        $recordCnt = $logEnabled ? RequestLog::count() : 0;

        $iconStatus = ReportData::INDICATOR_ICON_STATUS_INFO;

        if (!$logEnabled) {
            $iconStatus = ReportData::INDICATOR_ICON_STATUS_DISABLED;
        }
        elseif ($recordCnt > 0) {
            $iconStatus = ReportData::INDICATOR_ICON_STATUS_IMPORTANT;
        }

        $recordCnt = $recordCnt >= 1000 ? round($recordCnt / 1000) . 'K' : number_format($recordCnt, 0);

        $logHasRecords = $logEnabled ?
            $recordCnt :
            Lang::get('system::lang.dashboard.report_data_source.log_disabled');

        return $result->setRows($this->makeResultRow($dimension, [
            ReportData::METRIC_VALUE => $logHasRecords,
            ReportData::METRIC_INDICATOR_ICON_STATUS => $iconStatus,
            ReportData::METRIC_LINK_ENABLED => BackendAuth::userHasAccess('utilities.logs'),
            ReportData::METRIC_LINK_HREF => Backend::url('system/requestlogs')
        ]));
    }

    /**
     * getSystemWarnings
     */
    protected function getSystemWarnings()
    {
        return array_merge(
            $this->getSecurityWarnings(),
            $this->getExtensionWarnings(),
            $this->getPluginWarnings(),
            $this->getPathWarnings()
        );
    }

    /**
     * getSecurityWarnings
     */
    protected function getSecurityWarnings(): array
    {
        $warnings = [];

        if (Config::get('app.debug', true)) {
            $warnings[] = Lang::get('backend::lang.warnings.debug');
        }

        $backendUris = [
            'backend',
            'back-end',
            'login',
            'admin',
            'administration',
        ];

        $configUri = trim(ltrim((string) Config::get('backend.uri'), '/'));
        foreach ($backendUris as $uri) {
            if ($uri === $configUri) {
                $warnings[] = Lang::get('backend::lang.warnings.backend_uri', ['name' => '<strong>/'.$configUri.'</strong>']);
                break;
            }
        }

        $backendLogins = [
            'guest',
            'admin',
            'administrator',
            'root',
            'user'
        ];

        $foundLogins = Db::table('backend_users')->whereIn('login', $backendLogins)->pluck('login')->all();
        foreach ($foundLogins as $login) {
            $warnings[] = Lang::get('backend::lang.warnings.backend_login', ['name' => '<strong>'.$login.'</strong>']);
        }

        return $warnings;
    }

    /**
     * getExtensionWarnings
     */
    protected function getExtensionWarnings(): array
    {
        $warnings = [];
        $requiredExtensions = [
            'GD' => extension_loaded('gd'),
            'fileinfo' => extension_loaded('fileinfo'),
            'Zip' => class_exists('ZipArchive'),
            'cURL' => function_exists('curl_init') && defined('CURLOPT_FOLLOWLOCATION'),
            'OpenSSL' => function_exists('openssl_random_pseudo_bytes'),
        ];

        foreach ($requiredExtensions as $extension => $installed) {
            if (!$installed) {
                $warnings[] = Lang::get('backend::lang.warnings.extension', ['name' => '<strong>'.$extension.'</strong>']);
            }
        }

        return $warnings;
    }

    /**
     * getPluginWarnings
     */
    protected function getPluginWarnings(): array
    {
        $warnings = [];
        $missingPlugins = PluginManager::instance()->findMissingDependencies();

        foreach ($missingPlugins as $pluginCode) {
            $warnings[] = Lang::get('backend::lang.warnings.plugin_missing', ['name' => '<strong>'.$pluginCode.'</strong>']);
        }

        return $warnings;
    }

    /**
     * getPathWarnings
     */
    protected function getPathWarnings(): array
    {
        $warnings = [];
        $writablePaths = [
            temp_path(),
            storage_path(),
            storage_path('app'),
            storage_path('logs'),
            storage_path('framework'),
            storage_path('cms'),
            storage_path('cms/cache'),
            storage_path('cms/twig'),
            storage_path('cms/combiner'),
        ];

        if (System::hasModule('Cms')) {
            $writablePaths[] = themes_path();
        }

        foreach ($writablePaths as $path) {
            if (!is_writable($path)) {
                $warnings[] = Lang::get('backend::lang.warnings.permissions', ['name' => '<strong>'.$path.'</strong>']);
            }
        }

        return $warnings;
    }
}
