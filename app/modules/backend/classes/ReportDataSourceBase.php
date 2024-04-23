<?php namespace Backend\Classes;

use Backend\Classes\Dashboard\ReportDataOrderRule;
use Backend\Classes\Dashboard\ReportDataPaginationParams;
use Backend\Classes\Dashboard\ReportDateDataSet;
use Backend\Classes\Dashboard\ReportDimension;
use Backend\Classes\Dashboard\ReportDimensionFilter;
use Backend\Classes\Dashboard\ReportFetchDataResult;
use Backend\Classes\Dashboard\ReportMetric;
use Backend\Classes\Dashboard\ReportMetricConfiguration;
use Backend\Models\ReportDataCache;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use SystemException;

/**
 * ReportDataSourceBase class are used by report widgets to get their data.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
abstract class ReportDataSourceBase
{
    use \System\Traits\ViewMaker;

    const GROUP_INTERVAL_DAY = 'day';
    const GROUP_INTERVAL_WEEK = 'week';
    const GROUP_INTERVAL_MONTH = 'month';
    const GROUP_INTERVAL_QUARTER = 'quarter';
    const GROUP_INTERVAL_YEAR = 'year';
    const GROUP_INTERVAL_FULL = 'full';

    private $knownGroupIntervals = [
        self::GROUP_INTERVAL_DAY,
        self::GROUP_INTERVAL_WEEK,
        self::GROUP_INTERVAL_MONTH,
        self::GROUP_INTERVAL_QUARTER,
        self::GROUP_INTERVAL_YEAR,
        self::GROUP_INTERVAL_FULL
    ];

    /**
     * @var ReportDimension[]|null Cache of registered dimensions.
     */
    private $dimensions = null;

    /**
     * @var ReportMetric[] Cache of common registered metrics.
     */
    private $metrics = [];

    /**
     * Returns the data for the specified report.
     * @param string $dimensionCode Specifies the dimension to group the data by.
     * @param array $metricCodes Specifies the metrics to return.
     * @param ReportMetricConfiguration[] $metricsConfiguration Specifies the report metrics configuration.
     * @param ?Carbon $startDate Specifies the start date.
     * @param ?Carbon $endDate Specifies the end date.
     * @param ?int $startTimestamp Optional. Specifies the starting timestamp for relative intervals.
     * Either $startTimestamp must be set, or both $startDate and $endDate.
     * @param ReportDimensionFilter[] $dimensionFilters Specifies the filters to apply to the dimension values.
     * @param ?string $groupInterval Specifies the group interval.
     * One of the GROUP_INTERVAL_* constants.
     * Only applies if the dimension is a date dimension.
     * If not specified, the default group interval GROUP_INTERVAL_DAY will be used.
     * @param ?ReportDataOrderRule $orderRule Specifies the data ordering rule.
     * @param ?int $limit Specifies the maximum number of records to return.
     * @param ?ReportDataPaginationParams $paginationParams Specifies the pagination parameters.
     * Either $limit or $paginationParams or none can be set.
     * @param ReportDataCache $reportDataCache Specifies the cache to use.
     * @param bool $hideEmptyDimensionValues Indicates whether empty dimension values must be removed from the dataset.
     * @param bool $totalsOnly Indicates that the method should only return total values for metrics, and not rows.
     * @return ReportFetchDataResult Returns the result of fetching the report data.
     */
    public function getData(
        string $dimensionCode,
        array $metricCodes,
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
        ReportDataCache $reportDataCache,
        bool $totalsOnly
    ): ReportFetchDataResult {
        if ($limit !== null && $paginationParams) {
            throw new SystemException('Limit and pagination parameters cannot be both set.');
        }

        if (($startDate || $endDate) && $startTimestamp !== null) {
            throw new SystemException('Start and end dates cannot be set if the start timestamp is also set.');
        }

        if (!$startDate && $startTimestamp === null) {
            throw new SystemException('Either the start and end dates or the start timestamp must be set.');
        }

        $dimension = ReportDimension::findDimensionByCode(
            $this->getAvailableDimensions(),
            $dimensionCode
        );

        $metrics = [];
        foreach ($metricCodes as $metricCode) {
            $metric = ReportMetric::findMetricByCodeStrict(
                $dimension->getAvailableMetrics(),
                $metricCode,
                false
            );

            if (!$metric) {
                $metric = ReportMetric::findMetricByCodeStrict(
                    $this->getAvailableMetrics(),
                    $metricCode
                );
            }

            $metrics[] = $metric;
        }

        $this->assertValidOrderRule($orderRule, $dimension);
        $this->assertValidFilters($dimensionFilters, $dimension);

        $groupInterval = $this->validateDateGroupInterval($groupInterval, $dimension);

        $fetchResult = $this->fetchData(
            $dimension,
            $metrics,
            $metricsConfiguration,
            $startDate,
            $endDate,
            $startTimestamp,
            $dimensionFilters,
            $groupInterval,
            $orderRule,
            $limit,
            $paginationParams,
            $hideEmptyDimensionValues,
            $totalsOnly
        );

        if ($dimension->isDate() && $startDate) {
            $range = CarbonPeriod::create($startDate, $endDate);
            $dataset = new ReportDateDataSet($dimension, $metrics, $range, $orderRule, $groupInterval, $fetchResult->getRows());
            $rows = $dataset->getNormalizedData();
            $fetchResult->setRows($rows);
        }

        return $fetchResult;
    }

    /**
     * Returns the available dimensions for this data source.
     * Dimensions are attributes of data, e.g. date or "page_path".
     * @return ReportDimension[]
     */
    public function getAvailableDimensions(): array
    {
        if ($this->dimensions === null) {
            throw new SystemException('The dimensions are not registered in ' . get_class($this));
        }

        return $this->dimensions;
    }

    /**
     * Returns the available metrics for this data source.
     * Metrics are measures of data, e.g. "page views".
     * @return ReportMetric[]
     */
    public function getAvailableMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * Runs a data source event handler.
     * @param string $handlerName The name of the handler.
     * @return mixed The data to pass to the client-side handler caller.
     */
    public function runHandler(string $handlerName)
    {
        if (!preg_match('/^on/i', $handlerName)) {
            throw new SystemException('Invalid data source handler name ' . $handlerName);
        }

        if (!method_exists($this, $handlerName)) {
            throw new SystemException('Data source handler method doesn\'t exist ' . $handlerName);
        }

        return $this->$handlerName();
    }

    /**
     * Registers a new dimension.
     * @param ReportDimension $dimension Dimension to register
     * @return ReportDimension Returns the added dimension, for chaining
     */
    protected function registerDimension(ReportDimension $dimension): ReportDimension
    {
        if ($this->dimensions === null) {
            $this->dimensions = [];
        }

        $knownDimension = array_filter(
            $this->dimensions,
            fn ($item) => $item->getCode() === $dimension->getCode()
        );

        if (count($knownDimension)) {
            throw new SystemException('The dimension is already registered: ' . $dimension->getCode());
        }

        return $this->dimensions[] = $dimension;
    }

    /**
     * A shorthand version of registerDimension.
     * Adds a calculated dimension that doesn't have a corresponding database column.
     * @param string $code Specifies the dimension referral code.
     * For special dimension types, the code should begin with the respective type prefix,
     * for instance, `indicator@`. These special dimension types are defined by the
     * ReportDimension::TYPE_XXX constants.
     * @param string $displayName Specifies the dimension name used in reports.
     * @return ReportDimension Returns the added dimension, for chaining.
     */
    protected function addCalculatedDimension(string $code, string $displayName)
    {
        return $this->registerDimension(new ReportDimension(
            $code,
            $code,
            $displayName
        ));
    }

    /**
     * Registers a new common metric.
     * Common metrics can be used with any dimension provided by the data source.
     */
    protected function registerMetric(ReportMetric $metric)
    {
        $knownMetric = array_filter(
            $this->metrics,
            fn ($item) => $item->getCode() === $metric->getCode()
        );

        if (count($knownMetric)) {
            throw new SystemException('The metric is already registered: ' . $metric->getCode());
        }

        $this->metrics[] = $metric;
    }

    /**
     * Returns the data for the specified report.
     * @param ReportDimension $dimension Specifies the dimension to group the data by.
     * @param ReportMetric[] $metrics Specifies the metrics to return.
     * @param ReportMetricConfiguration[] $metricsConfiguration Specifies the report metrics configuration.
     * @param ?Carbon $startDate Specifies the start date.
     * @param ?Carbon $endDate Specifies the end date.
     * @param ?int $startTimestamp Optional. Specifies the starting timestamp for relative intervals.
     * Either $startTimestamp must be set, or both $startDate and $endDate.
     * @param ReportDimensionFilter[] $dimensionFilters Specifies the filters to apply to the dimension values.
     * @param ?string $groupInterval Specifies the group interval.
     * One of the GROUP_INTERVAL_* constants. Null for non-date dimensions.
     * @param ?ReportDataOrderRule $orderRule Specifies the data ordering rule.
     * @param ?int $limit Specifies the maximum number of records to return.
     * @param ?ReportDataPaginationParams $paginationParams Specifies the pagination parameters.
     * Either $limit or $paginationParams or none can be set.
     * @param bool $hideEmptyDimensionValues Indicates whether empty dimension values must be removed from the dataset.
     * @param bool $totalsOnly Indicates that the method should only return total values for metrics, and not rows.
     * @return ReportFetchDataResult Returns the result of fetching the report data.
     */
    abstract protected function fetchData(
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
    ): ReportFetchDataResult;

    /**
     * Helps format a result row expected in the fetchData return value.
     * @param ReportDimension $dimension Dimension corresponding to the data row.
     * @param array $metricsAndValues An associative array of metric codes and values.
     * The array key is the metric code. The method doesn't check if dimensions exist
     * in the data source.
     * @return array Returns a properly formatted data row
     */
    protected function makeResultRow(ReportDimension $dimension, array $metricsAndValues)
    {
        $result = [
            'oc_dimension' => $dimension->getCode()
        ];

        foreach ($metricsAndValues as $metricCode => $value) {
            if (!is_string($metricCode)) {
                throw new SystemException('Metric code must be a string.');
            }

            $result['oc_metric_'.$metricCode] = $value;
        }

        return [(object)$result];
    }

    /**
     * Validates the specified group interval.
     * @param string $groupInterval Specifies the group interval.
     * @param ReportDimension $dimension Specifies the dimension to group the data by.
     * @return ?string Returns the validated group interval.
     */
    private function validateDateGroupInterval(?string $groupInterval, ReportDimension $dimension): ?string
    {
        if (!strlen($groupInterval) && $dimension->isDate()) {
            $groupInterval = self::GROUP_INTERVAL_DAY;
        }

        if (strlen($groupInterval) && !$dimension->isDate()) {
            return null;
        }

        if (strlen($groupInterval) && !in_array($groupInterval, $this->knownGroupIntervals)) {
            throw new SystemException('Invalid group interval: ' . $groupInterval);
        }

        return $groupInterval;
    }

    private function assertValidOrderRule(?ReportDataOrderRule $orderRule, ReportDimension $dimension)
    {
        if ($orderRule && $orderRule->getDataAttributeType() === ReportDataOrderRule::ATTR_TYPE_DIMENSION_FIELD) {
            $dimensionField = $dimension->findDimensionFieldByCode($orderRule->getAttributeName());
            if (!$dimensionField->getIsSortable()) {
                $fieldCode = $dimensionField->getCode();
                throw new SystemException("Dimension field $fieldCode is not sortable.");
            }
        }
    }

    private function assertValidFilters(array $filters, ReportDimension $dimension)
    {
        foreach ($filters as $filter) {
            if ($filter->getDataAttributeType() !== ReportDataOrderRule::ATTR_TYPE_DIMENSION_FIELD) {
                continue;
            }

            $dimensionField = $dimension->findDimensionFieldByCode($filter->getAttributeName());
            if (!$dimensionField->getIsFilterable()) {
                $fieldCode = $dimensionField->getCode();
                throw new SystemException("Dimension field $fieldCode is not filterable.");
            }
        }
    }
}
