<?php

use Backend\Models\ReportDataCache;
use Backend\Classes\Dashboard\ReportMetric;
use Backend\Classes\Dashboard\ReportDimension;
use Backend\Classes\Dashboard\ReportDataOrderRule;
use Backend\Classes\Dashboard\ReportDataPaginationParams;
use Backend\Classes\Dashboard\ReportFetchDataResult;
use Backend\Classes\ReportDataSourceBase;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class ReportDataSourceBaseTest extends TestCase
{
    public function testNonCacheableData()
    {
        $startDate = new Carbon('2023-01-01');
        $endDate = new Carbon('2023-01-03');

        $dsRange = CarbonPeriod::create($startDate, $endDate);
        $values = [
            (object)['oc_dimension' => 'product-1', 'oc_metric_metric_1' => 1],
            (object)['oc_dimension' => 'product-2', 'oc_metric_metric_1' => 2],
            (object)['oc_dimension' => 'product-3', 'oc_metric_metric_1' => 3]
        ];

        $rangesAndValues = [
            ['range' => $dsRange, 'values' => $values]
        ];

        $reportDataCacheMock = $this->mockReportDataCache();

        $reportDataCacheMock->expects($this->never())->method('getRanges');
        $reportDataCacheMock->expects($this->never())->method('putRange');

        $dataSource = $this->getDataSource(
            new ReportDimension('product', 'product', 'Product'),
            $rangesAndValues
        );

        $result = $dataSource->getData(
            'product',
            ['metric_1'],
            [],
            $startDate,
            $endDate,
            null,
            [],
            null,
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_DIMENSION),
            null,  // Limit
            null,  // Pagination
            false,
            $reportDataCacheMock,
            false
        );

        $this->assertInstanceOf(ReportFetchDataResult::class, $result);
        $rows = $result->getRows();
        $this->assertIsArray($rows);
        $this->assertCount(3, $rows);
        $this->assertEquals($values, $rows);
    }

    private function getDataSource(ReportDimension $dimension, array $rangesAndValues): ReportDataSourceBase
    {
        return new class ($rangesAndValues, $dimension) extends ReportDataSourceBase {
            private $rangesAndValues;

            public function __construct($rangesAndValues, $dimension){
                $this->registerDimension($dimension);
                $this->registerMetric(
                    new ReportMetric('metric_1', 'metric_1', 'Metric 1', ReportMetric::AGGREGATE_SUM)
                );
                $this->rangesAndValues = $rangesAndValues;
            }

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
                foreach ($this->rangesAndValues as $rangeAndValues) {
                    $range = $rangeAndValues['range'];
                    $values = $rangeAndValues['values'];

                    if ($range->getStartDate()->eq($startDate)) {
                        return new ReportFetchDataResult($values);
                    }
                }

                return new ReportFetchDataResult;
            }
        };
    }

    private function mockReportDataCache(): ReportDataCache
    {
        return $this->getMockBuilder(ReportDataCache::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}