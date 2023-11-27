<?php namespace Tailor\ContentFields;

use October\Contracts\Element\FormElement;
use October\Contracts\Element\ListElement;

/**
 * DataTableField
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class DataTableField extends FallbackField
{
    /**
     * extendModelObject will extend the record model.
     */
    public function extendModelObject($model)
    {
        $model->addJsonable($this->fieldName);
    }

    /**
     * extendDatabaseTable adds any required columns to the database.
     */
    public function extendDatabaseTable($table)
    {
        $table->mediumText($this->fieldName)->nullable();
    }
}
