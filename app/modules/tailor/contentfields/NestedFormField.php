<?php namespace Tailor\ContentFields;

use Tailor\Models\RepeaterItem;
use Tailor\Models\NestedFormItem;
use Tailor\Classes\ContentFieldBase;
use Tailor\Classes\Relations\CustomFieldHasOneRelation;
use October\Contracts\Element\FormElement;
use October\Contracts\Element\ListElement;
use SystemException;

/**
 * NestedFormField
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class NestedFormField extends ContentFieldBase
{
    /**
     * @var array fieldsetConfig definition
     */
    public $fieldsetConfig;

    /**
     * defineConfig will process the field configuration.
     */
    public function defineConfig(array $config)
    {
        if (isset($config['form'])) {
            $this->fieldsetConfig = (array) $config['form'];
        }
    }

    /**
     * validateConfig
     */
    public function validateConfig()
    {
        if ($this->fieldsetConfig === null) {
            throw new SystemException('Repeater must specify either a "form" or "group" property for the form fields');
        }
    }

    /**
     * defineFormField will define how a field is displayed in a form.
     */
    public function defineFormField(FormElement $form, $context = null)
    {
        $form->addFormField($this->fieldName, $this->label)->defaultCreate()->useConfig($this->getCleanFormConfig());
    }

    /**
     * defineListColumn
     */
    public function defineListColumn(ListElement $list, $context = null)
    {
        if ($context === 'import' || $context === 'export') {
            $list->defineColumn($this->fieldName, $this->label);
        }
    }

    /**
     * getCleanFormConfig strips fields from the nested form configuration, since they
     * are provided by the RepeaterItem::defineFormFields model
     */
    protected function getCleanFormConfig(): array
    {
        $config = $this->config;

        if (isset($config['form'])) {
            $config['form']['fields'] = [];
            $config['form']['tabs']['fields'] = [];
            $config['form']['secondaryTabs']['fields'] = [];
        }

        return $config;
    }

    /**
     * extendModelObject will extend the record model.
     */
    public function extendModelObject($model)
    {
        // Define the relationship
        $model->hasOne[$this->fieldName] = [
            NestedFormItem::class,
            'key' => 'host_id',
            'delete' => true,
            'relationClass' => CustomFieldHasOneRelation::class
        ];

        // Pass the fieldset configuration to all related instances
        $model->bindEvent('model.afterRelation', function($name, $related) use ($model) {
            if ($name === $this->fieldName) {
                $related->setBlueprintFieldConfig(
                    $model,
                    $this->getRepeaterTableName($model),
                    $this->fieldName,
                    $this->fieldsetConfig,
                    false
                );
            }
        });
    }

    /**
     * getRepeaterTableName
     */
    protected function getRepeaterTableName($model)
    {
        if ($model instanceof RepeaterItem) {
            return $model->getTable();
        }

        return $model->getBlueprintDefinition()->getRepeaterTableName();
    }
}
