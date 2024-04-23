<?php namespace Cms\Classes;

use Cms\Models\MaintenanceSetting;
use Backend\Classes\Dashboard\ReportDataOrderRule;
use Backend\Classes\Dashboard\ReportDimension;
use Backend\Classes\Dashboard\ReportData;
use Backend\Classes\Dashboard\ReportDataPaginationParams;
use Backend\Classes\Dashboard\ReportFetchDataResult;
use Backend\Classes\ReportDataSourceBase;
use Carbon\Carbon;
use BackendAuth;
use Backend;
use Lang;

/**
 * CmsStatusDataSource providing information about the website status.
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class CmsStatusDataSource extends ReportDataSourceBase
{
    /**
     * @var string DIMENSION_CMS_INFORMATION
     */
    const DIMENSION_CMS_INFORMATION = 'indicator@cms_information';

    /**
     * __construct
     */
    public function __construct()
    {
        $langPrefix = 'cms::lang.dashboard.status_data_source.';

        ReportData::addIndicatorMetrics(
            $this->addCalculatedDimension(
                self::DIMENSION_CMS_INFORMATION,
                $langPrefix . 'dimension_cms_information'
            )
            ->setDefaultWidgetConfig([
                'icon'=>'ph ph-power',
                'title' =>Lang::get($langPrefix.'widgets.status.title'),
                'link_text'=>Lang::get($langPrefix.'widgets.status.link')
            ])
        );
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
        bool $totalsOnly
    ): ReportFetchDataResult {
        $maintenance = MaintenanceSetting::get('is_enabled');
        $value = $maintenance ?
            Lang::get('cms::lang.dashboard.status_data_source.status_maintenance') :
            Lang::get('cms::lang.dashboard.status_data_source.status_online');

        $iconStatus = $maintenance
            ? ReportData::INDICATOR_ICON_STATUS_IMPORTANT
            : ReportData::INDICATOR_ICON_STATUS_SUCCESS;

        $row = $this->makeResultRow($dimension, [
            ReportData::METRIC_VALUE => $value,
            ReportData::METRIC_INDICATOR_ICON_STATUS => $iconStatus,
            ReportData::METRIC_LINK_ENABLED => BackendAuth::userHasAccess('cms.themes'),
            ReportData::METRIC_LINK_HREF => Backend::url('system/settings/update/october/cms/maintenance_settings')
        ]);

        return new ReportFetchDataResult($row);
    }
}
