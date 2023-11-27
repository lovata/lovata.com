<?php namespace Backend\Models\ExportModel;

use Lang;
use Config;
use League\Csv\Writer as CsvWriter;
use League\Csv\CharsetConverter;
use SplTempFileObject;

/**
 * EncodesCsv format for export
 */
trait EncodesCsv
{
    /**
     * getFormatEncodingOptions returns all available encodings values from the localization config
     * @return array
     */
    public function getFormatEncodingOptions()
    {
        return (array) Config::get('backend.available_export_encodings', [
            'utf-8' => Lang::get('backend::lang.import_export.encodings.utf_8'),
            'us-ascii' => Lang::get('backend::lang.import_export.encodings.us_ascii'),
            'iso-8859-1' => Lang::get('backend::lang.import_export.encodings.iso_8859_1'),
            'iso-8859-2' => Lang::get('backend::lang.import_export.encodings.iso_8859_2'),
            'iso-8859-3' => Lang::get('backend::lang.import_export.encodings.iso_8859_3'),
            'iso-8859-4' => Lang::get('backend::lang.import_export.encodings.iso_8859_4'),
            'iso-8859-5' => Lang::get('backend::lang.import_export.encodings.iso_8859_5'),
            'iso-8859-6' => Lang::get('backend::lang.import_export.encodings.iso_8859_6'),
            'iso-8859-7' => Lang::get('backend::lang.import_export.encodings.iso_8859_7'),
            'iso-8859-8' => Lang::get('backend::lang.import_export.encodings.iso_8859_8'),
            'iso-8859-0' => Lang::get('backend::lang.import_export.encodings.iso_8859_0'),
            'iso-8859-10' => Lang::get('backend::lang.import_export.encodings.iso_8859_10'),
            'iso-8859-11' => Lang::get('backend::lang.import_export.encodings.iso_8859_11'),
            'iso-8859-13' => Lang::get('backend::lang.import_export.encodings.iso_8859_13'),
            'iso-8859-14' => Lang::get('backend::lang.import_export.encodings.iso_8859_14'),
            'iso-8859-15' => Lang::get('backend::lang.import_export.encodings.iso_8859_15'),
            'Windows-1250' => Lang::get('backend::lang.import_export.encodings.windows_1250'),
            'Windows-1251' => Lang::get('backend::lang.import_export.encodings.windows_1251'),
            'Windows-1252' => Lang::get('backend::lang.import_export.encodings.windows_1252'),
        ]);
    }

    /**
     * encodeArrayValueForCsv
     */
    protected function encodeArrayValueForCsv(array $data, $delimiter = '|')
    {
        // Multi dimension arrays have no choice but to base64 encode
        if (count($data) !== count($data, COUNT_RECURSIVE)) {
            return 'base64:' . base64_encode(json_encode($data));
        }

        // Implode single dimension array as a string
        $newData = [];
        foreach ($data as $value) {
            $newData[] = str_replace($delimiter, '\\'.$delimiter, $value);
        }

        return implode($delimiter, $newData);
    }

    /**
     * processExportDataAsCsv returns the export data as a CSV string
     */
    protected function processExportDataAsCsv($columns, $results, $options)
    {
        // Parse options
        $options = array_merge([
            'firstRowTitles' => true,
            'savePath' => null,
            'useOutput' => false,
            'fileName' => null,
            'delimiter' => null,
            'enclosure' => null,
            'escape' => null,
            'encoding' => null
        ], $options);

        // Prepare CSV
        if ($options['savePath']) {
            $csv = CsvWriter::createFromPath($options['savePath'], 'w+');
        }
        else {
            $csv = CsvWriter::createFromFileObject(new SplTempFileObject);
        }

        $csv->setOutputBOM(CsvWriter::BOM_UTF8);

        if ($options['delimiter'] !== null) {
            $csv->setDelimiter($options['delimiter']);
        }

        if ($options['enclosure'] !== null) {
            $csv->setEnclosure($options['enclosure']);
        }

        if ($options['escape'] !== null) {
            $csv->setEscape($options['escape']);
        }

        if ($options['encoding'] !== null) {
            CharsetConverter::addTo($csv, 'UTF-8', $options['encoding']);
        }

        // Add headers
        if ($options['firstRowTitles']) {
            $headers = $this->getColumnHeaders($columns);
            $csv->insertOne($headers);
        }

        // Add records
        foreach ($results as $result) {
            $data = $this->matchDataToColumns($result, $columns);
            $csv->insertOne($data);
        }

        // Output
        if ($options['useOutput']) {
            $csv->output($options['fileName']);
            return;
        }

        // Saved to file
        if ($options['savePath']) {
            return;
        }

        return $csv->toString();
    }
}
