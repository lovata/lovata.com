<?php namespace Tailor\ContentFields;

use Backend\FormWidgets\TagList;
use October\Contracts\Element\ListElement;
use October\Contracts\Element\FilterElement;
use October\Contracts\Element\FormElement;

/**
 * TagListField is locked to array mode for JSON query support
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class TagListField extends FallbackField
{
    /**
     * defineFormField will define how a field is displayed in a form.
     */
    public function defineFormField(FormElement $form, $context = null)
    {
        $form->addFormField($this->fieldName, $this->label)
            ->useConfig($this->config)
            ->mode(TagList::MODE_ARRAY)
        ;
    }

    /**
     * defineListColumn
     */
    public function defineListColumn(ListElement $list, $context = null)
    {
        if (is_array($this->column)) {
            $list->defineColumn($this->fieldName, $this->label)
                ->displayAs('selectable')
                ->shortLabel($this->shortLabel)
                ->options($this->options)
                ->useConfig($this->column ?: [])
            ;
        }
    }

    /**
     * defineFilterScope
     */
    public function defineFilterScope(FilterElement $filter, $context = null)
    {
        if (is_array($this->scope)) {
            // @deprecated move to the filter class. detect list array and combine there (v4)
            $options = $this->options;
            if ($options && !$this->useKey) {
                $options = array_combine($this->options, $this->options);
            }

            $filter->defineScope($this->fieldName, $this->label)
                ->displayAs('group')
                ->shortLabel($this->shortLabel)
                ->options($options)
                ->useConfig($this->scope ?: [])
            ;
        }
    }

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
