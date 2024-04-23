<?php

use Backend\Models\ImportModel;
use System\Models\File as FileModel;

class ExampleDbImportModel extends ImportModel
{
    public $rules = [];

    public function importData($results, $sessionKey = null)
    {
        return [];
    }
}

class ImportModelDbTest extends PluginTestCase
{
    public function testGetImportFilePath()
    {
        $model = new ExampleDbImportModel;
        $sessionKey = uniqid('session_key', true);

        $file1 = new FileModel;
        $file1->is_public = false;
        $file1->fromFile(base_path('modules/backend/tests/fixtures/reference/file1.txt'));
        $file1->save();

        $file2 = new FileModel;
        $file2->is_public = false;
        $file2->fromFile(base_path('modules/backend/tests/fixtures/reference/file2.txt'));
        $file2->save();

        $model->import_file()->add($file1, $sessionKey);
        $model->import_file()->add($file2, $sessionKey);

        $this->assertEquals(
            $file2->getLocalPath(),
            $model->getImportFilePath($sessionKey),
            'ImportModel::getImportFilePath() should return the last uploaded file.'
        );
    }
}
