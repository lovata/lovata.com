<?php namespace Backend\Classes\Dashboard;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Manages the report data cache.
 */
class ReportDataCacheHelper
{
    private $nonCacheableFunctions = [
        ReportMetric::AGGREGATE_AVG,
        ReportMetric::AGGREGATE_COUNT_DISTINCT
    ];

    /**
     * Determines where cache aggregation can be used for the given dimension and metrics.
     * @param ReportDimension $dimension Specifies the dimension.
     * @param array $metrics Specifies the metrics.
     */
    public function canAggregateCache(ReportDimension $dimension, array $metrics): bool
    {
        if ($dimension->getCode() !== ReportDimension::CODE_DATE) {
            return false;
        }

        foreach ($metrics as $metric) {
            if (in_array($metric->getAggregateFunction(), $this->nonCacheableFunctions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generates a sequence of date ranges to comprehensively cover the provided date span.
     * Takes into account existing dates within the range, excluding them and generating
     * sub-ranges accordingly.
     *
     * @param Carbon $dateStart The starting date of the range.
     * @param Carbon $dateEnd The ending date of the range.
     * @param array $existingDates An array of date strings that already exist within the date range.
     * @return CarbonPeriod[] An array of CarbonPeriod objects.
     */
    public function generateRanges(Carbon $dateStart, Carbon $dateEnd, $existingDates): array
    {
        $ranges = [];

        $period = CarbonPeriod::create($dateStart, $dateEnd);
        $rangeStartDate = null;
        foreach ($period as $date) {
            $dateString = $date->toDateString();

            if (!in_array($dateString, $existingDates)) {
                if ($rangeStartDate === null) {
                    $rangeStartDate = $date;
                }
            }
            else {
                if ($rangeStartDate !== null) {
                    $ranges[] = CarbonPeriod::create($rangeStartDate, $date->subDay());
                    $rangeStartDate = null;
                }
            }
        }

        if ($rangeStartDate !== null) {
            $ranges[] = CarbonPeriod::create($rangeStartDate, $date);
        }

        return $ranges;
    }

    /**
     * Creates a cache key for the given dimension, metrics and dimension filters.
     * @param string $dataSourceClassName Specifies the data source class name.
     * @param ReportDimension $dimension Specifies the dimension.
     * @param ReportMetric[] $metrics Specifies the metrics.
     * @param array $dimensionFilters Specifies the dimension filters.
     * @return string The cache key.
     */
    public function makeCacheKey(string $dataSourceClassName, ReportDimension $dimension, array $metrics, ?array $dimensionFilters): string
    {
        $keys = [];
        $keys[] = $dataSourceClassName;
        $keys[] = $dimension->getCacheUniqueCode();
        foreach ($metrics as $metric) {
            $keys[] = $metric->getCacheUniqueCode();
        }

        if ($dimensionFilters !== null) {
            foreach ($dimensionFilters as $filter) {
                $keys[] = $filter->getCacheUniqueCode();
            }
        }

        return md5(implode('-', $keys));
    }
}
