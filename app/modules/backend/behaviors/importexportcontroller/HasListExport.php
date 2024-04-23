<?php namespace Backend\Behaviors\ImportExportController;

use Lang;
use Response;
use ApplicationException;

/**
 * HasListExport contains logic for imports
 */
trait HasListExport
{
    /**
     * checkUseListExportMode
     */
    protected function checkUseListExportMode()
    {
        if (!$useList = $this->getConfig('export[useList]')) {
            return false;
        }

        if (!$this->controller->isClassExtendedWith(\Backend\Behaviors\ListController::class)) {
            throw new ApplicationException(__("You must implement the controller behavior ListController with the export 'useList' option enabled."));
        }

        if (is_array($useList)) {
            $listDefinition = array_get($useList, 'definition');
        }
        else {
            $listDefinition = $useList;
        }

        return $this->exportFromList($listDefinition);
    }

    /**
     * exportFromList outputs the list results as a CSV export.
     * @param string $definition
     * @param array $options
     */
    public function exportFromList($definition = null, $options = [])
    {
        $lists = $this->controller->makeLists();
        $widget = $lists[$definition] ?? reset($lists);

        // Parse options
        $options = array_merge([
            'fileFormat' => $this->getConfig('defaultFormatOptions[fileFormat]', 'csv'),
            'delimiter' => $this->getConfig('defaultFormatOptions[delimiter]', ','),
            'enclosure' => $this->getConfig('defaultFormatOptions[enclosure]', '"'),
            'escape' => $this->getConfig('defaultFormatOptions[escape]', '\\'),
            'encoding' => $this->getConfig('defaultFormatOptions[encoding]', 'utf-8'),
        ], $options);

        // Prepare output
        $fileFormat = $options['fileFormat'];
        $filename = e($this->makeExportFileName($fileFormat));

        // JSON
        if ($fileFormat === 'json') {
            return Response::make(
                $this->exportFromListAsJson($widget, $options),
                200,
                [
                    'Content-Type' => 'application/json',
                    'Content-Disposition' => sprintf('%s; filename="%s"', 'attachment', $filename)
                ]
            );
        }

        // CSV
        return Response::make(
            $this->exportFromListAsCsv($widget, $options),
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Disposition' => sprintf('%s; filename="%s"', 'attachment', $filename)
            ]
        );
    }
}
