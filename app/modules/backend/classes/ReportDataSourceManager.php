<?php namespace Backend\Classes;

use App;
use Lang;
use SystemException;
use Backend\Classes\ReportDataSourceBase;

/**
 * ReportDataSourceManager manages report data sources.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ReportDataSourceManager
{
    /**
     * instance creates a new instance of this singleton
     */
    public static function instance(): static
    {
        return App::make('backend.reports');
    }

    /**
     * @var string[]
     */
    private $dataSources = [];

    /**
     * Registers a report data source.
     * @param string $className A class name of a data source.
     * The class must extend Backend\Classes\ReportDataSourceBase
     * @param string $displayName The data source name to display in the user interface.
     */
    public function registerDataSourceClass(string $className, string $displayName): void
    {
        $this->dataSources[$className] = [
            'displayName' => $displayName
        ];
    }

    /**
     * Returns a data source instance by its class name.
     * @throws SystemException if the provided class name is not a subclass Backend\Classes\ReportDataSourceBase.
     * @param string $className A data source class name.
     * @return ?ReportDataSourceBase Returns the data source instance or null.
     */
    public function getDataSource(string $className): ?ReportDataSourceBase
    {
        if (!array_key_exists($className, $this->dataSources)) {
            return null;
        }

        if (!is_subclass_of($className, ReportDataSourceBase::class)) {
            throw new SystemException("The provided class is not a report data source: " . $className);
        }

        return new $className();
    }

    /**
     * Returns class and display names of registered data sources.
     * @return array
     */
    public function listDataSourceClasses(): array
    {
        $result = [];
        foreach ($this->dataSources as $className => $info) {
            $result[$className] = $info['displayName'];
        }
        return $result;
    }

    /**
     * Returns the default widget configuration for data source dimensions that have a defined type.
     * @return array
     */
    public function getDefaultWidgetConfigs(): array
    {
        $result = [];
        $dataSourceClasses = $this->listDataSourceClasses();
        foreach ($dataSourceClasses as $className => $displayName) {
            $dataSource = $this->getDataSource($className);
            if (!$dataSource) {
                continue;
            }

            $dimensions = $dataSource->getAvailableDimensions();
            foreach ($dimensions as $dimension) {
                $type = $dimension->getDimensionType();
                if (!strlen($type)) {
                    continue;
                }

                $defaultConfig = $dimension->getDefaultWidgetConfig();
                $defaultConfig['dimension'] = $dimension->getCode();
                $defaultConfig['data_source'] = $className;

                $dataSourceName = Lang::get($displayName);
                $menuItemData = [
                    'config' => $defaultConfig,
                    'dimension' => Lang::get($dimension->getDisplayName()),
                ];

                $result[$type] ??= [];
                $result[$type][$dataSourceName] ??= [];
                $result[$type][$dataSourceName][] = $menuItemData;
            }
        }

        return $result;
    }
}
