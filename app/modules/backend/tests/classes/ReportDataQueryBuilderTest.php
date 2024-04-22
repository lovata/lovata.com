<?php

use Backend\Classes\Dashboard\ReportMetric;
use Backend\Classes\Dashboard\ReportDimension;
use Backend\Classes\Dashboard\ReportDataOrderRule;
use Backend\Classes\Dashboard\ReportDimensionField;
use Backend\Classes\Dashboard\ReportDataQueryBuilder;
use Backend\Classes\Dashboard\ReportDimensionFilter;
use Backend\Classes\ReportDataSourceBase;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

class ReportDataQueryBuilderTest extends TestCase
{
    private $testData1 = [
        ['date_dimension' => '2023-01-01', 'int_metric' => 1, 'float_metric' => 1.1],
        ['date_dimension' => '2023-01-01', 'int_metric' => 2, 'float_metric' => 2.1],
        ['date_dimension' => '2023-01-02', 'int_metric' => 4, 'float_metric' => 3.3],
        ['date_dimension' => '2023-01-02', 'int_metric' => 3, 'float_metric' => 1.1],
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->createApplication();

        Schema::create('test_report_data', function($table)
        {
            $table->increments('id');
            $table->date('date_dimension');
            $table->integer('int_metric');
            $table->decimal('float_metric',  10, 2);
        });
    }

    public function testGetSingleMetricData()
    {
        Db::table('test_report_data')->insert($this->testData1);

        $dimension = new ReportDimension(ReportDimension::CODE_DATE, 'date_dimension', 'Date');
        $metric = new ReportMetric('max_int', 'int_metric', 'Metric 1', ReportMetric::AGGREGATE_MAX);

        $reportQueryBuilder = new ReportDataQueryBuilder(
            'test_report_data',
            $dimension,
            [$metric],
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_DIMENSION),
            null,
            null,
            null,  // Pagination
            ReportDataSourceBase::GROUP_INTERVAL_DAY,
            false,
            new Carbon('2023-01-01'),
            new Carbon('2023-01-31'),
            null,
            null,
            null,
            false
        );
        $dataArray = $reportQueryBuilder->initQuery()->get()->toArray();
        $this->assertCount(2, $dataArray);

        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_max_int'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_dimension'));

        $this->assertEquals(2, $dataArray[0]->oc_metric_max_int);
        $this->assertEquals('2023-01-01', $dataArray[0]->oc_dimension);
        $this->assertEquals(4, $dataArray[1]->oc_metric_max_int);
        $this->assertEquals('2023-01-02', $dataArray[1]->oc_dimension);
        $this->assertEquals(2, $reportQueryBuilder->getTotalRecords());
    }

    public function testFullDateGrouping()
    {
        Db::table('test_report_data')->insert($this->testData1);

        $dimension = new ReportDimension(ReportDimension::CODE_DATE, 'date_dimension', 'Date');
        $reportQueryBuilder = new ReportDataQueryBuilder(
            'test_report_data',
            $dimension,
            [
                new ReportMetric('max_int', 'int_metric', 'Metric 1', ReportMetric::AGGREGATE_MAX),
                new ReportMetric('total_int', 'int_metric', 'Total integer', ReportMetric::AGGREGATE_SUM)
            ],
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_DIMENSION),
            null,
            null,
            null,  // Pagination
            ReportDataSourceBase::GROUP_INTERVAL_FULL,
            false,
            new Carbon('2023-01-01'),
            new Carbon('2023-01-31'),
            null,
            null,
            null,
            false
        );
        $dataArray = $reportQueryBuilder->initQuery()->get()->toArray();
        $this->assertCount(1, $dataArray);

        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_max_int'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_total_int'));
        $this->assertFalse(property_exists($dataArray[0], 'oc_dimension'));

        $this->assertEquals(4, $dataArray[0]->oc_metric_max_int);
        $this->assertEquals(10, $dataArray[0]->oc_metric_total_int);
        $this->assertEquals(1, $reportQueryBuilder->getTotalRecords());
    }

    public function testGetMultipleMetricData()
    {
        Db::table('test_report_data')->insert($this->testData1);
        Db::table('test_report_data')->insert(
            ['date_dimension' => '2023-01-02', 'int_metric' => 5, 'float_metric' => 1.1]
        );

        $dimension = new ReportDimension(ReportDimension::CODE_DATE, 'date_dimension', 'Date');

        $reportQueryBuilder = new ReportDataQueryBuilder(
            'test_report_data',
            $dimension,
            [
                new ReportMetric('max_int', 'int_metric', 'Max integer', ReportMetric::AGGREGATE_MAX),
                new ReportMetric('min_int', 'int_metric', 'Min integer', ReportMetric::AGGREGATE_MIN),
                new ReportMetric('total_int', 'int_metric', 'Total integer', ReportMetric::AGGREGATE_SUM),
                new ReportMetric('max_float', 'float_metric', 'Max float', ReportMetric::AGGREGATE_MAX),
                new ReportMetric('record_count', 'id', 'Record count', ReportMetric::AGGREGATE_COUNT),
                new ReportMetric('distinct_floats', 'float_metric', 'Distinct floats', ReportMetric::AGGREGATE_COUNT_DISTINCT),
            ],
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_DIMENSION),
            null,
            null,
            null,  // Pagination
            ReportDataSourceBase::GROUP_INTERVAL_DAY,
            false,
            new Carbon('2023-01-01'),
            new Carbon('2023-01-31'),
            null,
            null,
            null,
            false
        );
        $dataArray = $reportQueryBuilder->initQuery()->get()->toArray();
        $this->assertCount(2, $dataArray);

        $this->assertTrue(property_exists($dataArray[0], 'oc_dimension'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_max_int'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_min_int'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_total_int'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_max_float'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_record_count'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_distinct_floats'));

        $this->assertEquals(2, $dataArray[0]->oc_metric_max_int);
        $this->assertEquals(1, $dataArray[0]->oc_metric_min_int);
        $this->assertEquals(3, $dataArray[0]->oc_metric_total_int);
        $this->assertEquals(2.1, $dataArray[0]->oc_metric_max_float);
        $this->assertEquals(2, $dataArray[0]->oc_metric_record_count);
        $this->assertEquals(2, $dataArray[0]->oc_metric_distinct_floats);
        $this->assertEquals('2023-01-01', $dataArray[0]->oc_dimension);

        $this->assertEquals(5, $dataArray[1]->oc_metric_max_int);
        $this->assertEquals(3, $dataArray[1]->oc_metric_min_int);
        $this->assertEquals(12, $dataArray[1]->oc_metric_total_int);
        $this->assertEquals(3.3, $dataArray[1]->oc_metric_max_float);
        $this->assertEquals(3, $dataArray[1]->oc_metric_record_count);
        $this->assertEquals(2, $dataArray[1]->oc_metric_distinct_floats);
        $this->assertEquals('2023-01-02', $dataArray[1]->oc_dimension);
        $this->assertEquals(2, $reportQueryBuilder->getTotalRecords());
    }

    public function testDescendingSortByDimension()
    {
        Db::table('test_report_data')->insert($this->testData1);

        $dimension = new ReportDimension(ReportDimension::CODE_DATE, 'date_dimension', 'Date');
        $metric = new ReportMetric('max_int', 'int_metric', 'Metric 1', ReportMetric::AGGREGATE_MAX);

        $reportQueryBuilder = new ReportDataQueryBuilder(
            'test_report_data',
            $dimension,
            [$metric],
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_DIMENSION, null, false),
            null,
            null,
            null,  // Pagination
            ReportDataSourceBase::GROUP_INTERVAL_DAY,
            false,
            new Carbon('2023-01-01'),
            new Carbon('2023-01-31'),
            null,
            null,
            null,
            false
        );
        $dataArray = $reportQueryBuilder->initQuery()->get()->toArray();
        $this->assertCount(2, $dataArray);

        $this->assertEquals(2, $dataArray[1]->oc_metric_max_int);
        $this->assertEquals('2023-01-01', $dataArray[1]->oc_dimension);
        $this->assertEquals(4, $dataArray[0]->oc_metric_max_int);
        $this->assertEquals('2023-01-02', $dataArray[0]->oc_dimension);
        $this->assertEquals(2, $reportQueryBuilder->getTotalRecords());
    }

    public function testSortByMetric()
    {
        Db::table('test_report_data')->insert($this->testData1);

        $dimension = new ReportDimension(ReportDimension::CODE_DATE, 'date_dimension', 'Date');
        $metric = new ReportMetric('max_int', 'int_metric', 'Metric 1', ReportMetric::AGGREGATE_MAX);

        $reportQueryBuilder = new ReportDataQueryBuilder(
            'test_report_data',
            $dimension,
            [$metric],
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_METRIC, 'max_int', true),
            null,
            null,
            null,  // Pagination
            ReportDataSourceBase::GROUP_INTERVAL_DAY,
            false,
            new Carbon('2023-01-01'),
            new Carbon('2023-01-31'),
            null,
            null,
            null,
            false
        );
        $dataArray = $reportQueryBuilder->initQuery()->get()->toArray();
        $this->assertCount(2, $dataArray);

        $this->assertEquals(2, $dataArray[0]->oc_metric_max_int);
        $this->assertEquals('2023-01-01', $dataArray[0]->oc_dimension);
        $this->assertEquals(4, $dataArray[1]->oc_metric_max_int);
        $this->assertEquals('2023-01-02', $dataArray[1]->oc_dimension);
        $this->assertEquals(2, $reportQueryBuilder->getTotalRecords());

        // Descending

        $reportQueryBuilder = new ReportDataQueryBuilder(
            'test_report_data',
            $dimension,
            [$metric],
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_METRIC, 'max_int', false),
            null,
            null,
            null,  // Pagination
            ReportDataSourceBase::GROUP_INTERVAL_DAY,
            false,
            new Carbon('2023-01-01'),
            new Carbon('2023-01-31'),
            null,
            null,
            null,
            false
        );
        $dataArray = $reportQueryBuilder->initQuery()->get()->toArray();
        $this->assertCount(2, $dataArray);

        $this->assertEquals(2, $dataArray[1]->oc_metric_max_int);
        $this->assertEquals('2023-01-01', $dataArray[1]->oc_dimension);
        $this->assertEquals(4, $dataArray[0]->oc_metric_max_int);
        $this->assertEquals('2023-01-02', $dataArray[0]->oc_dimension);
        $this->assertEquals(2, $reportQueryBuilder->getTotalRecords());
    }

    public function testDimensionFields()
    {
        $queryBuilder = $this->makeBuilderWithDimensionFields([]);
        $query = $queryBuilder->initQuery();

        $dataArray = $query->get()->toArray();

        $this->assertCount(3, $dataArray); // We have 3 products and 4 sales
        $this->assertTrue(property_exists($dataArray[0], 'oc_dimension'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_field_product_name'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_field_product_code'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_sales_total'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_sales_count'));

        // The rows must be sorted by product name, descending
        $this->assertEquals(3, $dataArray[0]->oc_dimension);
        $this->assertEquals(2, $dataArray[1]->oc_dimension);
        $this->assertEquals(1, $dataArray[2]->oc_dimension);

        $this->assertEquals('Product 3 - pants', $dataArray[0]->oc_field_product_name);
        $this->assertEquals('Product 2 - t-shirt', $dataArray[1]->oc_field_product_name);
        $this->assertEquals('Product 1 - hoodie', $dataArray[2]->oc_field_product_name);

        $this->assertEquals('P3', $dataArray[0]->oc_field_product_code);
        $this->assertEquals('P2', $dataArray[1]->oc_field_product_code);
        $this->assertEquals('P1', $dataArray[2]->oc_field_product_code);

        $this->assertEquals(33, $dataArray[0]->oc_metric_sales_total);
        $this->assertEquals(44, $dataArray[1]->oc_metric_sales_total);
        $this->assertEquals(11, $dataArray[2]->oc_metric_sales_total);

        $this->assertEquals(1, $dataArray[0]->oc_metric_sales_count);
        $this->assertEquals(2, $dataArray[1]->oc_metric_sales_count);
        $this->assertEquals(1, $dataArray[2]->oc_metric_sales_count);

        $this->assertEquals(3, $queryBuilder->getTotalRecords());
    }

    public function testMetricsFromAnotherTable()
    {
        $this->makeSalesData();

        $reportQueryBuilder = new ReportDataQueryBuilder(
            'test_sales_products_data',
            new ReportDimension('product', 'test_sales_products_data.id', 'Product ID'),
            [
                new ReportMetric('sales_total', 'total', 'Sales total', ReportMetric::AGGREGATE_SUM),
            ],
            // Order by product id, ascending
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_DIMENSION, null, true),
            null,
            null,
            null,  // Pagination
            ReportDataSourceBase::GROUP_INTERVAL_DAY,
            false,
            new Carbon('2023-01-01'),
            new Carbon('2023-01-31'),
            null,
            null,
            null,
            false
        );

        $reportQueryBuilder->onConfigureMetrics(
            function(Builder $query, ReportDimension $dimension, array $metrics) {
                if (ReportMetric::findMetricByCode($metrics, 'sales_total')) {
                    $query->leftJoin('test_sales_report_data', function($join) {
                        $join->on('product_id', '=', 'test_sales_products_data.id')
                            ->where('test_sales_report_data.completed', '=', 1);
                    });
                }
            }
        );

        $query = $reportQueryBuilder->initQuery();
        $dataArray = $query->get()->toArray();

        $this->assertCount(3, $dataArray);
        $this->assertTrue(property_exists($dataArray[0], 'oc_dimension'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_sales_total'));

        $this->assertEquals(1, $dataArray[0]->oc_dimension);
        $this->assertEquals(2, $dataArray[1]->oc_dimension);
        $this->assertEquals(3, $dataArray[2]->oc_dimension);

        $this->assertEquals(11, $dataArray[0]->oc_metric_sales_total);
        $this->assertEquals(44, $dataArray[1]->oc_metric_sales_total);
        $this->assertEquals(33, $dataArray[2]->oc_metric_sales_total);
    }

    public function testManualMetrics()
    {
        $this->makeSalesData();

        $reportQueryBuilder = new ReportDataQueryBuilder(
            'test_sales_products_data',
            new ReportDimension('product', 'test_sales_products_data.id', 'Product ID'),
            [
                new ReportMetric('completed_sales_total', 'total_completed', 'Completed sales total', ReportMetric::AGGREGATE_SUM),
                new ReportMetric('abandoned_sales_total', 'total_abandoned', 'Abandoned sales total', ReportMetric::AGGREGATE_SUM),
            ],
            // Order by product id, ascending
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_DIMENSION, null, true),
            null,
            null,
            null,  // Pagination
            ReportDataSourceBase::GROUP_INTERVAL_DAY,
            false,
            new Carbon('2023-01-01'),
            new Carbon('2023-01-31'),
            null,
            null,
            null,
            false
        );

        $salesDataJoinAdded = new SplObjectStorage();
        $reportQueryBuilder->onConfigureMetric(
            function(Builder $query, ReportMetric $metric, ReportDimension $dimension, array $metrics)
                use (&$salesDataJoinAdded)
            {
                $isCompletedSales = $metric->getCode() === 'completed_sales_total';
                $isAbandonedSales = $metric->getCode() === 'abandoned_sales_total';

                $joinAdded = $salesDataJoinAdded->contains($query);
                if (($isCompletedSales || $isAbandonedSales) && !$joinAdded) {
                    $query->join('test_sales_report_data as tsrd', 'tsrd.product_id', '=', 'test_sales_products_data.id');
                    $salesDataJoinAdded[$query] = true;
                }

                if ($isCompletedSales) {
                    $query->addSelect(
                        Db::raw('SUM(CASE WHEN tsrd.completed = 1 THEN tsrd.total ELSE 0 END) as oc_metric_completed_sales_total')
                    );
                    return true;
                }

                if ($isAbandonedSales) {
                    $query->addSelect(
                        Db::raw('SUM(CASE WHEN tsrd.completed = 0 THEN tsrd.total ELSE 0 END) as oc_metric_abandoned_sales_total')
                    );
                    return true;
                }
            }
        );

        $query = $reportQueryBuilder->initQuery();
        $dataArray = $query->get()->toArray();

        $this->assertCount(3, $dataArray);
        $this->assertTrue(property_exists($dataArray[0], 'oc_dimension'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_completed_sales_total'));
        $this->assertTrue(property_exists($dataArray[0], 'oc_metric_abandoned_sales_total'));

        $this->assertEquals(1, $dataArray[0]->oc_dimension);
        $this->assertEquals(2, $dataArray[1]->oc_dimension);
        $this->assertEquals(3, $dataArray[2]->oc_dimension);

        $this->assertEquals(11, $dataArray[0]->oc_metric_completed_sales_total);
        $this->assertEquals(44, $dataArray[1]->oc_metric_completed_sales_total);
        $this->assertEquals(33, $dataArray[2]->oc_metric_completed_sales_total);

        $this->assertEquals(11, $dataArray[0]->oc_metric_abandoned_sales_total);
        $this->assertEquals(22, $dataArray[1]->oc_metric_abandoned_sales_total);
        $this->assertEquals(0, $dataArray[2]->oc_metric_abandoned_sales_total);
        $this->assertEquals(3, $reportQueryBuilder->getTotalRecords());
    }

    public function testDimensionFilterEquals()
    {
        $queryBuilder = $this->makeBuilderWithDimensionFields(
            [$this->makeDimensionFilter(ReportDimensionFilter::OPERATION_EQUALS, 1)]
        );
        $query = $queryBuilder->initQuery();
        $dataArray = $query->get()->toArray();

        $this->assertCount(1, $dataArray);
        $this->assertEquals(1, $dataArray[0]->oc_dimension);
        $this->assertEquals(1, $queryBuilder->getTotalRecords());
    }

    public function testDimensionFilterMoreOrEquals()
    {
        $queryBuilder = $this->makeBuilderWithDimensionFields(
            [$this->makeDimensionFilter(ReportDimensionFilter::OPERATION_MORE_OR_EQUALS, 2)]
        );
        $query = $queryBuilder->initQuery();
        $dataArray = $query->get()->toArray();

        $this->assertCount(2, $dataArray);
        $this->assertEquals(3, $dataArray[0]->oc_dimension);
        $this->assertEquals(2, $dataArray[1]->oc_dimension);
        $this->assertEquals(2, $queryBuilder->getTotalRecords());
    }

    public function testDimensionFilterLessOrEquals()
    {
        $queryBuilder = $this->makeBuilderWithDimensionFields(
            [$this->makeDimensionFilter(ReportDimensionFilter::OPERATION_LESS_OR_EQUALS, 2)]
        );
        $query = $queryBuilder->initQuery();
        $dataArray = $query->get()->toArray();

        $this->assertCount(2, $dataArray);
        $this->assertEquals(2, $dataArray[0]->oc_dimension);
        $this->assertEquals(1, $dataArray[1]->oc_dimension);
        $this->assertEquals(2, $queryBuilder->getTotalRecords());
    }

    public function testDimensionFilterMore()
    {
        $queryBuilder = $this->makeBuilderWithDimensionFields(
            [$this->makeDimensionFilter(ReportDimensionFilter::OPERATION_MORE, 2)]
        );
        $query = $queryBuilder->initQuery();
        $dataArray = $query->get()->toArray();

        $this->assertCount(1, $dataArray);
        $this->assertEquals(3, $dataArray[0]->oc_dimension);
        $this->assertEquals(1, $queryBuilder->getTotalRecords());
    }

    public function testDimensionFilterLess()
    {
        $queryBuilder = $this->makeBuilderWithDimensionFields(
            [$this->makeDimensionFilter(ReportDimensionFilter::OPERATION_LESS, 2)]
        );
        $query = $queryBuilder->initQuery();
        $dataArray = $query->get()->toArray();

        $this->assertCount(1, $dataArray);
        $this->assertEquals(1, $dataArray[0]->oc_dimension);
        $this->assertEquals(1, $queryBuilder->getTotalRecords());
    }

    public function testDimensionFilterOneOf()
    {
        $queryBuilder = $this->makeBuilderWithDimensionFields(
            [$this->makeDimensionFilter(ReportDimensionFilter::OPERATION_ONE_OF, [1, 3])]
        );
        $query = $queryBuilder->initQuery();

        $dataArray = $query->get()->toArray();

        $this->assertCount(2, $dataArray);
        $this->assertEquals(3, $dataArray[0]->oc_dimension);
        $this->assertEquals(1, $dataArray[1]->oc_dimension);
        $this->assertEquals(2, $queryBuilder->getTotalRecords());
    }

    public function testDimensionFieldFilterStringStartsWith()
    {
        $queryBuilder = $this->makeBuilderWithDimensionFields(
            [$this->makeDimensionFieldFilter(ReportDimensionFilter::OPERATION_STARTS_WITH, 'Product 1 - hood')]
        );
        $query = $queryBuilder->initQuery();

        $dataArray = $query->get()->toArray();

        $this->assertCount(1, $dataArray);
        $this->assertEquals(1, $dataArray[0]->oc_dimension);
        $this->assertEquals(1, $queryBuilder->getTotalRecords());
    }

    public function testDimensionFieldFilterStringIncludes()
    {
        $queryBuilder = $this->makeBuilderWithDimensionFields(
            [$this->makeDimensionFieldFilter(ReportDimensionFilter::OPERATION_STRING_INCLUDES, 't-shirt')]
        );
        $query = $queryBuilder->initQuery();

        $dataArray = $query->get()->toArray();

        $this->assertCount(1, $dataArray);
        $this->assertEquals(2, $dataArray[0]->oc_dimension);
        $this->assertEquals(1, $queryBuilder->getTotalRecords());
    }

    private function makeSalesData()
    {
        Schema::create('test_sales_report_data', function($table)
        {
            $table->increments('id');
            $table->integer('product_id');
            $table->date('date_dimension');
            $table->boolean('completed');
            $table->decimal('total',  10, 2);
        });

        Schema::create('test_sales_products_data', function($table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('sku');
            $table->decimal('price',  10, 2);
        });

        Db::table('test_sales_products_data')->insert([
            ['name' => 'Product 1 - hoodie', 'sku' => 'P1', 'price' => 10, 'id' => 1],
            ['name' => 'Product 2 - t-shirt', 'sku' => 'P2', 'price' => 20, 'id' => 2],
            ['name' => 'Product 3 - pants', 'sku' => 'P3', 'price' => 30, 'id' => 3]
        ]);

        Db::table('test_sales_report_data')->insert([
            ['product_id' => 1, 'date_dimension' => '2020-01-01', 'total' => 11, 'completed' => 1],
            ['product_id' => 2, 'date_dimension' => '2020-01-02', 'total' => 22, 'completed' => 1],
            ['product_id' => 2, 'date_dimension' => '2020-01-02', 'total' => 22, 'completed' => 1],
            ['product_id' => 3, 'date_dimension' => '2020-01-03', 'total' => 33, 'completed' => 1],
            ['product_id' => 1, 'date_dimension' => '2020-01-04', 'total' => 11, 'completed' => 0],
            ['product_id' => 2, 'date_dimension' => '2020-01-04', 'total' => 22, 'completed' => 0]
        ]);
    }

    private function makeBuilderWithDimensionFields(array $filters)
    {
        $this->makeSalesData();

        $dimension = new ReportDimension('product', 'product_id', 'Product');
        $dimension->addDimensionField(
            new ReportDimensionField('oc_field_product_name', 'Product name', null, true, false),
            new ReportDimensionField('oc_field_product_sku', 'Product SKU', null, true, false),
        );

        $reportQueryBuilder = new ReportDataQueryBuilder(
            'test_sales_report_data',
            $dimension,
            [
                new ReportMetric('sales_total', 'total', 'Sales total', ReportMetric::AGGREGATE_SUM),
                new ReportMetric('sales_count', 'product_id', 'Quantity', ReportMetric::AGGREGATE_COUNT)
            ],
            // Order by product name, descending
            new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_DIMENSION_FIELD, 'oc_field_product_name', false),
            $filters,
            null,
            null,  // Pagination
            ReportDataSourceBase::GROUP_INTERVAL_DAY,
            false,
            new Carbon('2023-01-01'),
            new Carbon('2023-01-31'),
            null,
            null,
            null,
            false
        );

        $reportQueryBuilder->onConfigureQuery(
            function(Builder $query, ReportDimension $dimension, array $metrics) {
                // The report query builder doesn't load dimension fields
                // automatically, so we need to manually add them.
                $query->join(
                    'test_sales_products_data',
                    'test_sales_products_data.id',
                    '=',
                    'test_sales_report_data.product_id');

                $query->addSelect([
                    'test_sales_products_data.name as oc_field_product_name',
                    'test_sales_products_data.sku as oc_field_product_code'
                ]);

                $query->where('test_sales_report_data.completed', true);
            }
        );

        return $reportQueryBuilder;
    }

    private function makeDimensionFilter(string $operation, string|array|int|float|bool $value)
    {
        return new ReportDimensionFilter(
            ReportDimensionFilter::ATTR_TYPE_DIMENSION,
            null,
            $operation,
            $value
        );
    }

    private function makeDimensionFieldFilter(string $operation, string|array|int|float|bool $value)
    {
        return new ReportDimensionFilter(
            ReportDimensionFilter::ATTR_TYPE_DIMENSION_FIELD,
            'oc_field_product_name',
            $operation,
            $value
        );
    }
}