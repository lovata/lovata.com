<?php namespace Backend\Controllers\Index;

use Str;
use Lang;
use Backend\Classes\Controller;
use Backend\Classes\DashboardManager;
use Backend\Classes\Dashboard\ReportPeriodCalculator;
use Backend\Classes\Dashboard\ReportDataOrderRule;
use Backend\Classes\Dashboard\ReportDataPaginationParams;
use Backend\Classes\Dashboard\ReportDimension;
use Backend\Classes\Dashboard\ReportDimensionFilter;
use Backend\Classes\Dashboard\ReportMetric;
use Backend\Classes\Dashboard\ReportMetricConfiguration;
use Backend\Classes\ReportDataSourceBase;
use Backend\Classes\ReportDataSourceManager;
use Backend\Models\ReportDataCache;
use Carbon\Carbon;
use SystemException;

/**
 * DashboardHandler handles requests to dashboard widgets.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class DashboardHandler
{
    use \System\Traits\PropertyContainer;

    const INTERVAL_TYPE_DASHBOARD = 'dashboard';
    const INTERVAL_TYPE_YEAR = 'year';
    const INTERVAL_TYPE_QUARTER = 'quarter';
    const INTERVAL_TYPE_MONTH = 'month';
    const INTERVAL_TYPE_WEEK = 'week';
    const INTERVAL_TYPE_DAYS = 'days';
    const INTERVAL_TYPE_HOUR = 'hour';

    private $knownDateIntervalValues = [
        DashboardHandler::INTERVAL_TYPE_DASHBOARD,
        DashboardHandler::INTERVAL_TYPE_YEAR,
        DashboardHandler::INTERVAL_TYPE_QUARTER,
        DashboardHandler::INTERVAL_TYPE_MONTH,
        DashboardHandler::INTERVAL_TYPE_WEEK,
        DashboardHandler::INTERVAL_TYPE_DAYS,
        DashboardHandler::INTERVAL_TYPE_HOUR,
    ];

    public function getWidgetData(
        Controller $controller,
        array $widgetConfig,
        string $dashboardDateStart,
        string $dashboardDateEnd,
        string $aggregationInterval,
        bool $resetCache,
        string $dimensionCode,
        array $metricCodes,
        array $extraData,
        ?string $compare
    ): mixed {
        $staticWidgetHtml = $this->renderStaticWidget($controller, $widgetConfig);
        if ($staticWidgetHtml !== false) {
            return $staticWidgetHtml;
        }

        $limit = $this->getRequestedLimit($widgetConfig);
        $orderRule = $this->makeRequestedOrderRule($widgetConfig);
        $hideEmptyDimensionValues = $this->getRequestedHideEmptyDimensionValues($widgetConfig);
        $dataSource = $this->getRequestedDataSource($widgetConfig);
        $paginationParams = $this->getRequestedPaginationParams($extraData, $widgetConfig);
        $metricsConfiguration = $this->getRequestedMetricsConfiguration($metricCodes, $widgetConfig);
        $filters = $this->getRequestedWidgetFilters($widgetConfig);

        [$dateStart, $dateEnd, $startTimestamp] = $this->getRequestedDateInterval(
            $widgetConfig,
            $dashboardDateStart,
            $dashboardDateEnd
        );

        [$compareDateStart, $compareDateEnd] = $this->getRequestedCompareInterval($dateStart, $dateEnd, $compare);

        $fetchDataResult = $dataSource->getData(
            $dimensionCode,
            $metricCodes,
            $metricsConfiguration,
            $dateStart,
            $dateEnd,
            $startTimestamp,
            $filters,
            $aggregationInterval,
            $orderRule,
            $limit,
            $paginationParams,
            $hideEmptyDimensionValues,
            new ReportDataCache(),
            false
        );

        $prevIntervalFetchDataResult = null;
        if ($compareDateStart && $compareDateEnd) {
            $prevIntervalFetchDataResult = $dataSource->getData(
                $dimensionCode,
                $metricCodes,
                $metricsConfiguration,
                $compareDateStart,
                $compareDateEnd,
                null,
                $filters,
                $aggregationInterval,
                $orderRule,
                $limit,
                $paginationParams,
                $hideEmptyDimensionValues,
                new ReportDataCache(),
                false // Individual rows for previous periods are not yet supported
            );
        }

        $metricsData = $this->getDataSourceDimensionMetricData($dataSource, $dimensionCode);
        $dimensionData = $this->getDataSourceDimensionAndFields($dataSource, $dimensionCode);
        $dimensionFieldsData = $this->getDataSourceDimensionFieldsData($dataSource, $dimensionCode);

        $result = [
            'current' => [
                'widget_data' => $fetchDataResult->getRows(),
                'total_records' => $fetchDataResult->getTotalRecords(),
                'metric_totals' => $fetchDataResult->getMetricTotals(),
            ],
            'metrics_data' => $metricsData,
            'dimension_fields_data' => $dimensionFieldsData,
            'dimension_data' => $dimensionData
        ];

        if ($prevIntervalFetchDataResult) {
            $result['previous'] = [
                'total_records' => $prevIntervalFetchDataResult->getTotalRecords(),
                'metric_totals' => $prevIntervalFetchDataResult->getMetricTotals(),
                // 'widget_data' => $prevIntervalFetchDataResult->getRows() // Not yet supported
            ];

            $result['prev_date_start'] = $compareDateStart->toDateString();
            $result['prev_date_end'] = $compareDateEnd->toDateString();
        }

        return $result;
    }

    public function onGetWidgetCustomData(
        Controller $controller,
        array $widgetConfig,
        string $dashboardDateStart,
        string $dashboardDateEnd,
        string $aggregationInterval,
        bool $resetCache,
        array $extraData,
        ?string $compare
    ): mixed {
        [$dateStart, $dateEnd, $startTimestamp] = $this->getRequestedDateInterval(
            $widgetConfig,
            $dashboardDateStart,
            $dashboardDateEnd
        );

        [$compareDateStart, $compareDateEnd] = $this->getRequestedCompareInterval($dateStart, $dateEnd, $compare);

        $widgetClass = $this->getRequestedWidgetClass($widgetConfig);

        $widget = DashboardManager::instance()->getWidget($widgetClass, $controller);
        if (!$widget) {
            throw new SystemException('Widget class not found.');
        }

        $data = $widget->getData(
            $widgetConfig,
            $dateStart,
            $dateEnd,
            $startTimestamp,
            $compareDateStart,
            $compareDateEnd,
            $aggregationInterval,
            $extraData
        );

        return [
            'data' => $data
        ];
    }

    public function runDataSourceHandler(Controller $controller, string $handlerName, array $widgetConfig)
    {
        $dataSource = $this->getRequestedDataSource($widgetConfig);

        return $dataSource->runHandler($handlerName);
    }

    public function runCustomWidgetHandler(Controller $controller, string $handlerName, array $widgetConfig, array $extraData)
    {
        $widgetClass = $this->getRequestedWidgetClass($widgetConfig);

        $widget = DashboardManager::instance()->getWidget($widgetClass, $controller);
        if (!$widget) {
            throw new SystemException('Widget class not found.');
        }

        $result = $widget->runHandler(
            $widgetConfig,
            $handlerName,
            $extraData
        );

        return $result;
    }

    /**
     * getPropertyOptions returns options for multi-option properties (drop-downs, etc.)
     * @param string $property Specifies the property name
     * @return array Return an array of option values and descriptions
     */
    public function getPropertyOptions($property)
    {
        $allowedDimensionTypes = post('allowed_dimension_types');
        $allowedDimensionTypesArray = [];
        if (strlen($allowedDimensionTypes)) {
            $allowedDimensionTypesArray = explode(',', $allowedDimensionTypes);
        }

        if ($property === 'data_source') {
            return $this->getDataSources($allowedDimensionTypesArray);
        }

        if ($property === 'dimension') {
            return $this->getDataSourceDimensions($allowedDimensionTypesArray);
        }

        if ($property === 'metric' || Str::endsWith($property, '_metric')) {
            return $this->getRequestedDataSourceDimensionMetrics();
        }

        if ($property === 'sort_by') {
            return $this->getDataSourceSortByOptions();
        }

        if ($property === 'dimension_fields') {
            return $this->getRequestedDataSourceDimensionFields();
        }

        if ($property === 'filter_attribute') {
            return $this->getRequestedDataSourceFilterAttributes();
        }

        return [];
    }

    private function getDataSources(array $allowedDimensionTypesArray)
    {
        $dataSourceManager = ReportDataSourceManager::instance();
        $dataSourceClasses = $dataSourceManager->listDataSourceClasses();

        $result = [];
        foreach ($dataSourceClasses as $className => $displayName) {
            $allowedDimensions = $this->getDataSourceDimensions($allowedDimensionTypesArray, $className);
            if (!count($allowedDimensions)) {
                continue;
            }

            $result[$className] = $displayName;
        }

        return $result;
    }

    private function getDataSourceDimensions(array $allowedDimensionTypesArray, ?string $dataSourceClass = null)
    {
        $dataSourceClass = $dataSourceClass ?? post('data_source');
        if (!strlen($dataSourceClass)) {
            return [];
        }

        $dimensionsLimited = count($allowedDimensionTypesArray) > 0;
        $dataSource = $this->makeDataSource($dataSourceClass);
        $dimensions = $dataSource->getAvailableDimensions();
        $result = [];
        foreach ($dimensions as $dimension) {
            $dimensionType = $dimension->getDimensionType();
            if (
                $dimensionsLimited &&
                (strlen($dimensionType) && !in_array($dimensionType, $allowedDimensionTypesArray))
            ) {
                continue;
            }

            if (!$dimensionsLimited && strlen($dimensionType)) {
                continue;
            }

            $result[$dimension->getCode()] = $dimension->getDisplayName();
        }

        return $result;
    }

    private function getRequestedDataSourceDimensionMetrics()
    {
        $dataSourceClass = post('data_source');
        $dimensionCode = post('dimension');

        if (!strlen($dataSourceClass) || !strlen($dimensionCode)) {
            return [];
        }

        $dataSource = $this->makeDataSource($dataSourceClass);
        return $this->getDataSourceDimensionMetrics($dataSource, $dimensionCode);
    }

    private function getRequestedDataSourceFilterAttributes()
    {
        $dataSourceClass = post('data_source');
        $dimensionCode = post('dimension');
        $dataSource = $this->makeDataSource($dataSourceClass);
        $dimension = $this->findDimension($dataSource, $dimensionCode, false);
        if (!$dimension) {
            return [];
        }

        $result = [];
        if (!$dimension->isDate()) {
            // Date dimensions can't be filtered. They must use the
            // dashboard or widget interval settings.
            $result['oc_dimension'] = Lang::get($dimension->getDisplayName());
        }

        $dimensionFields = $this->getRequestedDataSourceDimensionFields(true);
        foreach ($dimensionFields as $code=>$title) {
            $result[$code] = $title;
        }

        return $result;
    }

    private function getRequestedDataSourceDimensionFields($filterableOnly = false)
    {
        $dataSourceClass = post('data_source');
        $dimensionCode = post('dimension');

        if (!strlen($dataSourceClass) || !strlen($dimensionCode)) {
            return [];
        }

        $dataSource = $this->makeDataSource($dataSourceClass);
        $dimension = $this->findDimension($dataSource, $dimensionCode, false);
        if (!$dimension) {
            return [];
        }

        $fields = $dimension->getDimensionFields();
        $result = [];
        foreach ($fields as $field) {
            if (!($filterableOnly && !$field->getIsFilterable())) {
                $result[$field->getCode()] = Lang::get($field->getDisplayName());
            }
        }

        return $result;
    }

    private function getDataSourceDimensionMetrics(ReportDataSourceBase $dataSource, string $dimensionCode): array
    {
        $allMetrics = $this->listAllMetrics($dataSource, $dimensionCode);
        $result = [];
        foreach ($allMetrics as $metric) {
            $result[$metric->getCode()] = Lang::get($metric->getDisplayName());
        }

        return $result;
    }

    private function listAllMetrics(ReportDataSourceBase $dataSource, string $dimensionCode): array
    {
        $metrics = $dataSource->getAvailableMetrics();
        if (strlen($dimensionCode)) {
            $dimension = $this->findDimension($dataSource, $dimensionCode, false);
            if ($dimension) {
                $dimensionMetrics = $dimension->getAvailableMetrics();
                $metrics = array_merge($metrics, $dimensionMetrics);
            }
        }

        return $metrics;
    }

    private function getDataSourceDimensionMetricData(ReportDataSourceBase $dataSource, string $dimensionCode): array
    {
        $result = [];
        $allMetrics = $this->listAllMetrics($dataSource, $dimensionCode);
        foreach ($allMetrics as $metric) {
            $result[$metric->getCode()] = [
                'label' => Lang::get($metric->getDisplayName()),
                'format_options' => $metric->getIntlFormatOptions()
            ];
        }

        return $result;
    }

    private function getDataSourceDimensionFieldsData($dataSource, $dimensionCode)
    {
        $dimension = $this->findDimension($dataSource, $dimensionCode, false);
        if (!$dimension) {
            return [];
        }

        $fields = $dimension->getDimensionFields();
        $result = [];
        foreach ($fields as $field) {
            $result[$field->getCode()] = Lang::get($field->getDisplayName());
        }

        return $result;
    }

    private function getDataSourceDimensionAndFields(ReportDataSourceBase $dataSource, string $dimensionCode): array
    {
        $result = [];

        $dimension = $this->findDimension($dataSource, $dimensionCode, false);
        if ($dimension) {
            $result[$dimension->getDataSetColumName()] = Lang::get($dimension->getDisplayName());
        }

        return $result;
    }

    private function makeDataSource(string $dataSourceClass): ReportDataSourceBase
    {
        $dataSourceManager = ReportDataSourceManager::instance();
        $dataSource = $dataSourceManager->getDataSource($dataSourceClass);
        if (!$dataSource) {
            throw new SystemException('Data source class not found.');
        }

        return $dataSource;
    }

    private function findDimension(ReportDataSourceBase $dataSource, string $dimensionCode, bool $strict): ?ReportDimension
    {
        return ReportDimension::findDimensionByCode($dataSource->getAvailableDimensions(), $dimensionCode, $strict);
    }

    private function findMetric(ReportDataSourceBase $dataSource, ReportDimension $dimension, string $metricCode): ReportMetric
    {
        $metric = ReportMetric::findMetricByCodeStrict($dimension->getAvailableMetrics(), $metricCode, false);
        if ($metric) {
            return $metric;
        }

        return ReportMetric::findMetricByCodeStrict($dataSource->getAvailableMetrics(), $metricCode, true);
    }

    private function getDataSourceSortByOptions()
    {
        $dimensionCode = post('dimension');
        $metricsConfig = post('metrics');
        $dataSourceClass = post('data_source');

        if (!$dimensionCode || !$metricsConfig) {
            return [];
        }

        $dataSource = $this->makeDataSource($dataSourceClass);
        $dimension = $this->findDimension($dataSource, $dimensionCode, false);
        if (!$dimension) {
            return [];
        }

        $result = [
            'oc_dimension' => Lang::get($dimension->getDisplayName()),
        ];

        if ($dimension->isDate()) {
            return $result;
        }

        foreach ($metricsConfig as $metricConfig) {
            $metric = $this->findMetric($dataSource, $dimension, $metricConfig['metric']);
            $result['oc_metric-' . $metric->getCode()] = Lang::get($metric->getDisplayName());
        }

        $fields = $dimension->getDimensionFields();
        foreach ($fields as $field) {
            $result[$field->getCode()] = Lang::get($field->getDisplayName());
        }

        return $result;
    }

    private function getRequestedLimit(array $widgetConfig): ?int
    {
        if (!array_key_exists('limit', $widgetConfig)) {
            return null;
        }

        return strlen($widgetConfig['limit']) ? (int)$widgetConfig['limit'] : null;
    }

    private function makeRequestedOrderRule(array $widgetConfig): ReportDataOrderRule
    {
        if (!array_key_exists('sort_by', $widgetConfig) || !array_key_exists('sort_order', $widgetConfig)) {
            return new ReportDataOrderRule(ReportDataOrderRule::ATTR_TYPE_DIMENSION);
        }

        $sortBy = $widgetConfig['sort_by'];
        $sortOrder = $widgetConfig['sort_order'];
        return ReportDataOrderRule::createFromWidgetConfig($sortOrder, $sortBy);
    }

    private function getRequestedHideEmptyDimensionValues(array $widgetConfig): bool
    {
        return array_key_exists('empty_dimension_values', $widgetConfig)
            && $widgetConfig['empty_dimension_values'] === 'hide';
    }

    private function renderStaticWidget(Controller $controller, array $widgetConfig): mixed
    {
        if (!isset($widgetConfig['type']) || $widgetConfig['type'] !== 'static') {
            return false;
        }

        $className = isset($widgetConfig['widget_class']) ? $widgetConfig['widget_class'] : null;
        if ($className !== null) {
            $container = new StaticReportWidgetContainer($controller);
            return $container->renderWidget($className, $widgetConfig);
        }
        else {
            throw new SystemException('Static widget class is not set');
        }
    }

    private function getRequestedPaginationParams(array $extraData, array $widgetConfig): ?ReportDataPaginationParams
    {
        if (!isset($widgetConfig['records_per_page']) || !strlen($widgetConfig['records_per_page'])) {
            return null;
        }

        if (!array_key_exists('current_page', $extraData)) {
            throw new SystemException('Current page is not set for a paginated query');
        }

        return new ReportDataPaginationParams(
            (int)$widgetConfig['records_per_page'],
            (int)$extraData['current_page']
        );
    }

    private function getRequestedDataSource(array $widgetConfig): ReportDataSourceBase
    {
        if (!isset($widgetConfig['data_source'])) {
            throw new SystemException('Report widget data source class is not set');
        }

        return $this->makeDataSource($widgetConfig['data_source']);
    }

    private function getRequestedMetricsConfiguration(array $metricCodes, array $widgetConfig): array
    {
        $result = [];
        foreach ($metricCodes as $metricCode) {
            $result[$metricCode] = $this->findMetricInWidgetConfig($metricCode, $widgetConfig);
        }

        return $result;
    }

    private function getRequestedWidgetFilters(array $widgetConfig): array
    {
        if (!array_key_exists('filters', $widgetConfig)) {
            return [];
        }

        $filters = $widgetConfig['filters'];
        if (!is_array($filters)) {
            return [];
        }

        $result = [];
        foreach ($filters as $filterConfig) {
            $attribute = $filterConfig['filter_attribute'];
            $attributeType = $attribute === 'oc_dimension' ?
                ReportDimensionFilter::ATTR_TYPE_DIMENSION :
                ReportDimensionFilter::ATTR_TYPE_DIMENSION_FIELD;

            $attrName = $attributeType === ReportDimensionFilter::ATTR_TYPE_DIMENSION ?
                null :
                $attribute;

            $operation = $filterConfig['operation'];
            $value = null;
            if ($operation !== ReportDimensionFilter::OPERATION_ONE_OF) {
                $value = $filterConfig['value_scalar'];
            } else {
                $value = $filterConfig['value_array'];
                $value = array_filter(array_map('trim', explode("\n", $value)), 'strlen');
            }

            $filter = new ReportDimensionFilter(
                $attributeType,
                $attrName,
                $operation,
                $value
            );

            $result[] = $filter;
        }

        return $result;
    }

    private function findMetricInWidgetConfig(string $code, array $widgetConfig): ?ReportMetricConfiguration
    {
        if (!isset($widgetConfig['metrics'])) {
            return new ReportMetricConfiguration(false, false);
        }

        foreach ($widgetConfig['metrics'] as $metricData) {
            $metricCode = isset($metricData['metric']) ? $metricData['metric'] : null;
            if (!$metricCode || $metricCode !== $code) {
                continue;
            }

            $displayTotals = isset($metricData['display_totals']) ? $metricData['display_totals'] : false;
            $displayRelativeBar = isset($metricData['display_relative_bar']) ? $metricData['display_relative_bar'] : false;

            return new ReportMetricConfiguration($displayTotals, $displayRelativeBar);
        }

        return new ReportMetricConfiguration(false, false);
    }

    private function getRequestedDateInterval(
        array $widgetConfig,
        string $dashboardDateStart,
        string $dashboardDateEnd
    ): array {
        $widgetInterval = isset($widgetConfig['date_interval']) ?
            $widgetConfig['date_interval'] :
            DashboardHandler::INTERVAL_TYPE_DASHBOARD;

        $dateStart = null;
        $startTimestamp = null;
        $dateEnd = Carbon::now();

        switch ($widgetInterval) {
            case DashboardHandler::INTERVAL_TYPE_DASHBOARD:
                $dateStart = Carbon::parse($dashboardDateStart);
                $dateEnd = Carbon::parse($dashboardDateEnd);
                break;
            case DashboardHandler::INTERVAL_TYPE_YEAR:
                $dateStart = Carbon::now()->startOfYear();
                break;
            case DashboardHandler::INTERVAL_TYPE_QUARTER:
                $dateStart = Carbon::now()->startOfQuarter();
                break;
            case DashboardHandler::INTERVAL_TYPE_MONTH:
                $dateStart = Carbon::now()->startOfMonth();
                break;
            case DashboardHandler::INTERVAL_TYPE_WEEK:
                $dateStart = Carbon::now()->startOfWeek(Carbon::MONDAY); // TODO
                break;
            case DashboardHandler::INTERVAL_TYPE_HOUR:
                $dateEnd = null;
                $startTimestamp = time() - 3600;
                break;
            case DashboardHandler::INTERVAL_TYPE_DAYS:
                $days = isset($widgetConfig['date_interval_days'])
                    ? $widgetConfig['date_interval_days']
                    : 1;
                $dateStart = Carbon::now()->subDays($days - 1);
                break;
            default:
                throw new SystemException('Unknown widget report interval '.$widgetInterval);
        }

        return [
            $dateStart,
            $dateEnd,
            $startTimestamp
        ];
    }

    private function getRequestedCompareInterval(?Carbon $dateStart, ?Carbon $dateEnd, ?string $compare): array
    {
        if (!strlen($compare)) {
            return [null, null];
        }

        if (!$dateStart && $dateEnd) {
            return [null, null];
        }

        if (!in_array($compare, ['prev-period', 'prev-year'])) {
            throw new SystemException('Invalid compare specifier');
        }

        $periodCalculator = new ReportPeriodCalculator();

        if ($compare === 'prev-period') {
            $range = $periodCalculator->getPreviousPeriod($dateStart, $dateEnd);
        }
        else {
            $range = $periodCalculator->getPreviousPeriodLastYear($dateStart, $dateEnd);
        }

        if (!$range) {
            return [null, null];
        }

        return [$range->getStartDate(), $range->getEndDate()];
    }

    private function getRequestedWidgetClass(array $widgetConfig): string
    {
        if (!isset($widgetConfig['type'])) {
            throw new SystemException('Custom widget class is not set');
        }

        return str_replace('-', '\\', $widgetConfig['type']);
    }
}
