<?php namespace Backend\Models\ExportModel;

use File;
use Response;

/**
 * EncodesJson format for import
 */
trait EncodesJson
{
    /**
     * encodeArrayValueForJson
     */
    protected function encodeArrayValueForJson(array $data)
    {
        return $data;
    }

    /**
     * processExportDataAsJson returns the export data as a JSON string
     */
    protected function processExportDataAsJson($columns, $results, $options)
    {
        // Parse options
        $options = array_merge([
            'savePath' => null,
            'useOutput' => false,
            'customJson' => false,
        ], $options);

        $result = [];
        $encodeFlags = JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT;

        // Raw output
        if ($options['customJson']) {
            $result = $results;
        }
        // Compiled output
        else {
            foreach ($results as $data) {
                $row = [];
                foreach ($columns as $column => $label) {
                    $row[$column] = array_get($data, $column);
                }

                $result[] = $row;
            }
        }

        // Output
        if ($options['useOutput']) {
            Response::json($result)->send();
            return;
        }

        // Save to file
        if ($options['savePath']) {
            File::put($options['savePath'], json_encode($result, $encodeFlags));
            return;
        }

        return json_encode($result, $encodeFlags);
    }
}
