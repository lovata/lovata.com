<?php namespace Tailor\ContentFields;

use Tailor\Classes\ContentFieldBase;
use October\Contracts\Element\FormElement;
use October\Contracts\Element\ListElement;
use October\Contracts\Element\FilterElement;

/**
 * GenericField is used for generic form field types, like text, password, etc
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class GenericField extends ContentFieldBase
{
    /**
     * defineFormField will define how a field is displayed in a form.
     */
    public function defineFormField(FormElement $form, $context = null)
    {
        $form->addFormField($this->fieldName, $this->label)->useConfig($this->config);
    }

    /**
     * defineListColumn will define how a field is displayed in a list.
     */
    public function defineListColumn(ListElement $list, $context = null)
    {
        $displayAs = $this->getDefaultColumnDisplayType();
        if (!$displayAs) {
            return;
        }

        $column = $list->defineColumn($this->fieldName, $this->label)->displayAs($displayAs);

        if ($this->options !== null) {
            $column->options($this->options);
        }

        if ($this->shortLabel !== null) {
            $column->shortLabel($this->shortLabel);
        }

        if (is_array($this->column)) {
            $column->useConfig($this->column);
        }
        elseif ($this->isColumnDefaultInvisible()) {
            $column->invisible();
        }
    }

    /**
     * defineFilterScope will define how a field is displayed in a filter.
     */
    public function defineFilterScope(FilterElement $filter, $context = null)
    {
        $displayAs = $this->getDefaultScopeDisplayType();
        if (!$displayAs) {
            return;
        }

        $scope = $filter->defineScope($this->fieldName, $this->label)->displayAs($displayAs);

        if ($this->options !== null) {
            $scope->options($this->options);
        }

        if ($this->shortLabel !== null) {
            $scope->shortLabel($this->shortLabel);
        }

        if (is_array($this->scope)) {
            $scope->useConfig($this->scope);
        }
    }

    /**
     * extendModelObject will extend the record model.
     */
    public function extendModelObject($model)
    {
        switch ($this->type) {
            case 'checkboxlist':
                $model->addJsonable($this->fieldName);
                break;
        }
    }

    /**
     * extendDatabaseTable
     */
    public function extendDatabaseTable($table)
    {
        switch ($this->type) {
            case 'checkbox':
            case 'switch':
                $table->boolean($this->fieldName)->nullable();
                break;

            case 'textarea':
            case 'checkboxlist':
                $table->mediumText($this->fieldName)->nullable();
                break;

            default:
                $table->text($this->fieldName)->nullable();
                break;
        }
    }

    /**
     * getDefaultColumnDisplayType
     */
    protected function getDefaultScopeDisplayType()
    {
        switch ($this->type) {
            case 'checkbox':
            case 'switch':
                return 'switch';

            case 'dropdown':
            case 'radio':
                return 'group';

            default:
                return null;
        }
    }

    /**
     * getDefaultColumnDisplayType
     */
    protected function getDefaultColumnDisplayType()
    {
        switch ($this->type) {
            case 'checkbox':
            case 'switch':
                return 'switch';

            case 'number':
                return 'number';

            case 'textarea':
                return 'summary';

            case 'checkboxlist':
            case 'balloon-selector':
            case 'dropdown':
            case 'radio':
                return 'selectable';

            case 'password':
                return null;

            default:
                return 'text';
        }
    }

    /**
     * isColumnDefaultInvisible
     */
    protected function isColumnDefaultInvisible()
    {
        switch ($this->type) {
            case 'textarea':
                return true;

            default:
                return false;
        }
    }
}
