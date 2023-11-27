<?php namespace Tailor\ContentFields;

use October\Contracts\Element\ListElement;
use October\Contracts\Element\FilterElement;

/**
 * NumberField
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class NumberField extends FallbackField
{
    /**
     * defineListColumn
     */
    public function defineListColumn(ListElement $list, $context = null)
    {
        $list->defineColumn($this->fieldName, $this->label)
            ->displayAs('number')
            ->shortLabel($this->shortLabel)
            ->useConfig($this->column ?: [])
        ;
    }

    /**
     * defineFilterScope
     */
    public function defineFilterScope(FilterElement $filter, $context = null)
    {
        $filter->defineScope($this->fieldName, $this->label)
            ->displayAs('number')
            ->shortLabel($this->shortLabel)
            ->useConfig($this->scope ?: [])
        ;
    }

    /**
     * extendDatabaseTable adds any required columns to the database.
     */
    public function extendDatabaseTable($table)
    {
        $table->integer($this->fieldName)->nullable();
    }
}
