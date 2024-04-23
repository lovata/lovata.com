<?php namespace Cms\Classes;

use Db;
use Backend\Classes\ReportDataSourceBase;
use Backend\Classes\Dashboard\ReportDataOrderRule;
use Backend\Classes\Dashboard\ReportDataQueryBuilder;
use Backend\Classes\Dashboard\ReportDimension;
use Backend\Classes\Dashboard\ReportDataPaginationParams;
use Backend\Classes\Dashboard\ReportFetchDataResult;
use Backend\Classes\Dashboard\ReportMetric;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

/**
 * CmsReportDataSource providing information about the website traffic.
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class CmsReportDataSource extends ReportDataSourceBase
{
    const DIMENSION_DATE = 'date';
    const DIMENSION_CITY = 'city';
    const DIMENSION_PAGE_PATH = 'page_path';
    const DIMENSION_REFERRAL_DOMAIN = 'referral_domain';
    const METRIC_PAGEVIEWS = 'pageviews';
    const METRIC_UNIQUE_VISITORS = 'unique_visitors';

    /**
     * __construct the data source
     */
    public function __construct()
    {
        $langPrefix = 'cms::lang.dashboard.report_data_source.';

        $this->registerDimension(new ReportDimension(
            ReportDimension::CODE_DATE,
            'ev_date',
            'backend::lang.dashboard.dimension_date'
        ))->setDateIntervalGroupingFields(
            'ev_year_week',
            'ev_year_month',
            'ev_year_quarter',
            'ev_year'
        );

        /*
        $this->registerDimension(new ReportDimension(
            CmsReportDataSource::DIMENSION_CITY,
            'city',
            $langPrefix.'dimension_city'
        ))->addDimensionField(new ReportDimensionField(
            'oc_field_country',
            $langPrefix.'dimension_country',
            'country',
            true,
            true
        ));
        */

        $this->registerDimension(new ReportDimension(
            CmsReportDataSource::DIMENSION_PAGE_PATH,
            'page_path',
            $langPrefix.'dimension_page_path'
        ));

        $this->registerDimension(new ReportDimension(
            CmsReportDataSource::DIMENSION_REFERRAL_DOMAIN,
            'referral_domain',
            $langPrefix.'dimension_referral_domain'
        ));

        $this->registerMetric(new ReportMetric(
            self::METRIC_PAGEVIEWS,
            'id',
            $langPrefix.'metric_pageviews',
            ReportMetric::AGGREGATE_COUNT
        ));

        $this->registerMetric(new ReportMetric(
            self::METRIC_UNIQUE_VISITORS,
            'client_id',
            $langPrefix.'metric_unique_visitors',
            ReportMetric::AGGREGATE_COUNT_DISTINCT
        ));
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
        $reportQueryBuilder = new ReportDataQueryBuilder(
            'cms_traffic_stats_pageviews',
            $dimension,
            $metrics,
            $orderRule,
            $dimensionFilters,
            $limit,
            $paginationParams,
            $groupInterval,
            $hideEmptyDimensionValues,
            $startDate,
            $endDate,
            $startTimestamp,
            'ev_date',
            'ev_timestamp',
            $totalsOnly
        );

        $reportQueryBuilder->onConfigureQuery(
            function(Builder $query, ReportDimension $dimension, array $metrics) {
                if ($dimension->getCode() === CmsReportDataSource::DIMENSION_CITY) {
                    $query->addSelect([
                        Db::raw('max(cms_traffic_stats_pageviews.country) as oc_field_country')
                    ]);
                }
            }
        );

        return $reportQueryBuilder->getFetchDataResult($metricsConfiguration);
    }
}
