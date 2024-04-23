<?php

use Carbon\Carbon;
use Backend\Classes\Dashboard\ReportMetric;
use Backend\Classes\Dashboard\ReportDimension;
use Backend\Classes\Dashboard\ReportDataCacheHelper;

class ReportDataCacheHelperTest extends TestCase
{
    public function testCanAggregateCache()
    {
        $dimension = new ReportDimension(ReportDimension::CODE_DATE, 'date', 'Date');
        $metrics = [
            new ReportMetric('metric_1', 'metric_1', 'Metric 1', ReportMetric::AGGREGATE_SUM),
            new ReportMetric('metric_1', 'metric_1', 'Metric 1', ReportMetric::AGGREGATE_MIN),
            new ReportMetric('metric_1', 'metric_1', 'Metric 1', ReportMetric::AGGREGATE_MAX),
            new ReportMetric('metric_1', 'metric_1', 'Metric 1', ReportMetric::AGGREGATE_COUNT),
        ];

        $manager = new ReportDataCacheHelper();
        $this->assertTrue($manager->canAggregateCache($dimension, $metrics));
    }

    public function testCantAggregateCacheMetric()
    {
        $dimension = new ReportDimension(ReportDimension::CODE_DATE, 'date', 'Date');
        $metrics = [
            new ReportMetric('metric_1', 'metric_1', 'Metric 1', ReportMetric::AGGREGATE_SUM),
            new ReportMetric('metric_1', 'metric_1', 'Metric 1', ReportMetric::AGGREGATE_AVG)
        ];

        $manager = new ReportDataCacheHelper();
        $this->assertFalse($manager->canAggregateCache($dimension, $metrics));
    }

    public function testCantAggregateCacheDimension()
    {
        $dimension = new ReportDimension('product', 'product', 'Product');
        $metrics = [
            new ReportMetric('metric_1', 'metric_1', 'Metric 1', ReportMetric::AGGREGATE_SUM)
        ];

        $manager = new ReportDataCacheHelper();
        $this->assertFalse($manager->canAggregateCache($dimension, $metrics));
    }

    public function testRangeWithoutExistingDates() {
        $manager = new ReportDataCacheHelper();

        $dateStart = new Carbon('2023-01-01');
        $dateEnd = new Carbon('2023-01-05');

        $ranges = $manager->generateRanges($dateStart, $dateEnd, []);

        $this->assertCount(1, $ranges);
        $this->assertEquals($dateStart, $ranges[0]->getStartDate());
        $this->assertEquals($dateEnd, $ranges[0]->getEndDate());
    }

    public function testRangeWithExistingDates() {
        $manager = new ReportDataCacheHelper();

        $dateStart = new Carbon('2023-01-01');
        $dateEnd = new Carbon('2023-01-05');
        $existingDates = ['2023-01-02', '2023-01-04'];

        $ranges = $manager->generateRanges($dateStart, $dateEnd, $existingDates);

        $this->assertCount(3, $ranges);

        $this->assertEquals(new Carbon('2023-01-01'), $ranges[0]->getStartDate());
        $this->assertEquals(new Carbon('2023-01-01'), $ranges[0]->getEndDate());

        $this->assertEquals(new Carbon('2023-01-03'), $ranges[1]->getStartDate());
        $this->assertEquals(new Carbon('2023-01-03'), $ranges[1]->getEndDate());

        $this->assertEquals(new Carbon('2023-01-05'), $ranges[2]->getStartDate());
        $this->assertEquals(new Carbon('2023-01-05'), $ranges[2]->getEndDate());
    }

    public function testSingleDateRangeWithoutExistingDates()
    {
        $dateStart = Carbon::create('2023-01-01');
        $dateEnd = Carbon::create('2023-01-01');
        $existingDates = [];

        $manager = new ReportDataCacheHelper();
        $ranges = $manager->generateRanges($dateStart, $dateEnd, $existingDates);

        $this->assertCount(1, $ranges);
        $this->assertTrue($ranges[0]->getStartDate()->equalTo($dateStart));
        $this->assertTrue($ranges[0]->getEndDate()->equalTo($dateEnd));
    }

    public function testSingleDayRangeWithExistingDate()
    {
        $dateStart = Carbon::create('2023-01-01');
        $dateEnd = Carbon::create('2023-01-01');

        $manager = new ReportDataCacheHelper();
        $ranges = $manager->generateRanges($dateStart, $dateEnd, ['2023-01-01']);

        $this->assertCount(0, $ranges);
    }

    public function testAllDatesAreExistingDates() {
        $dateStart = Carbon::create('2023-01-01');
        $dateEnd = Carbon::create('2023-01-03');

        $manager = new ReportDataCacheHelper();
        $ranges = $manager->generateRanges($dateStart, $dateEnd, ['2023-01-01', '2023-01-02', '2023-01-03']);

        $this->assertCount(0, $ranges);
    }

    public function testStartAndEndDatesAreExistingDates()
    {
        $dateStart = Carbon::create('2023-06-01');
        $dateEnd = Carbon::create('2023-06-30');
        $existingDates = [$dateStart->toDateString(), $dateEnd->toDateString()];

        $manager = new ReportDataCacheHelper();
        $ranges = $manager->generateRanges($dateStart, $dateEnd, $existingDates);

        $this->assertCount(1, $ranges);

        $this->assertTrue($ranges[0]->getStartDate()->equalTo($dateStart->addDay()));
        $this->assertTrue($ranges[0]->getEndDate()->equalTo($dateEnd->subDay()));
    }
}