<?php namespace Backend\Widgets\Form;

use Backend\Classes\FormField;
use October\Rain\Element\Form\FieldDefinition;
use October\Rain\Element\Form\FieldsetDefinition;
use SystemException;

/**
 * IsFormElement defines all methods to satisfy the FormElement contract
 *
 * @see \October\Contracts\Element\FormElement
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait IsFormElement
{
    /**
     * @var FieldsetDefinition|null activeTabSection where fields are currently being added
     */
    protected $activeTabSection = null;

    /**
     * addFormField adds a field to the fieldset
     */
    public function addFormField(string $fieldName = null, string $label = null): FieldDefinition
    {
        if ($this->activeTabSection === null) {
            throw new SystemException("The addFormField method should be called when defining fields inside a model. Try using addField, addTabField or addSecondaryTabField instead.");
        }

        $fieldObj = new FormField([
            'fieldName' => $fieldName,
            'label' => $label,
            'arrayName' => $this->arrayName,
            'idPrefix' => $this->getId()
        ]);

        $this->allFields[$fieldName] = $fieldObj;

        $this->activeTabSection->addField($fieldName, $fieldObj);

        return $fieldObj;
    }

    /**
     * getFormFieldset returns the current fieldset definition
     */
    public function getFormFieldset(): FieldsetDefinition
    {
        return $this->activeTabSection;
    }

    /**
     * getFormContext returns the current form context
     */
    public function getFormContext()
    {
        return $this->getContext();
    }
}
