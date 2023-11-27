<?php namespace Backend\Behaviors\ImportExportController;

use File;

/**
 * CanFormatJson contains logic for JSON files
 */
trait CanFormatJson
{
    /**
     * getImportFileColumnsFromJson
     */
    protected function getImportFileColumnsFromJson($path)
    {
        $jsonPath = File::get($path);

        $contents = json_decode($jsonPath, true);

        if ($contents === null) {
            return [];
        }

        if (!is_array($contents)) {
            return [];
        }

        $firstRow = array_first($contents);
        if (!is_array($firstRow)) {
            return [];
        }

        return array_keys($firstRow);
    }

    /**
     * getImportSampleColumnsFromJson
     */
    protected function getImportSampleColumnsFromJson($path, $columnIndex)
    {
        $jsonPath = File::get($path);

        $contents = json_decode($jsonPath, true);

        $result = [];

        $limit = 0;
        foreach ($contents as $content) {
            $count = 0;
            foreach ($content as $key => $val) {
                if ($count === $columnIndex) {
                    $result[] = $val;
                    break;
                }
                $count++;
            }
            $limit++;

            if ($limit > 50) {
                break;
            }
        }

        return $result;
    }

    /**
     * exportFromListAsJson
     */
    protected function exportFromListAsJson($widget, $options): string
    {
        $jsonResult = [];

        // Locate columns from widget
        $columns = $widget->getVisibleColumns();

        // Add records
        $getter = $this->getConfig('export[useList][raw]', false)
            ? 'getColumnValueRaw'
            : 'getColumnValue';

        $query = $widget->prepareQuery();
        $results = $query->get();

        if ($event = $widget->fireSystemEvent('backend.list.extendRecords', [&$results])) {
            $results = $event;
        }

        foreach ($results as $result) {
            $record = [];
            foreach ($columns as $column) {
                $value = $widget->$getter($result, $column);
                if (is_array($value)) {
                    $value = implode('|', $value);
                }
                $record[] = $value;
            }
            $jsonResult[] = $record;
        }

        return json_encode($jsonResult, JSON_PRETTY_PRINT);
    }
}
