<?php namespace System\Classes;

use Event;
use October\Contracts\Element\FormElement;

/**
 * DriverBehavior base class.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class DriverBehavior extends ModelBehavior
{
    use \System\Traits\ConfigMaker;

    /**
     * @var string driverFields defines form fields for this driver
     */
    public $driverFields = 'fields.yaml';

    /**
     * @var mixed fieldConfig is extra configuration for the converter type.
     */
    protected $fieldConfig;

    /**
     * __construct
     */
    public function __construct($model = null)
    {
        parent::__construct($model);

        $this->configPath = $this->guessConfigPathFrom($this);

        if ($model) {
            $this->initDriverHost($model);
        }
    }

    /**
     * initDriverHost when the converter is first created.
     * @param \Model $host
     */
    public function initDriverHost($host)
    {
    }

    /**
     * validateDriverHost validates the host data before saving, this method
     * can throw a ValidationException for invalid fields. For example:
     *
     *     throw new \ValidationException(['max_weight' => 'Max weight cannot be less than Min weight!']);
     *
     * @param \Model $host
     */
    public function validateDriverHost($host)
    {
    }

    /**
     * defineDriverFormFields should be called externally on the form widget.
     * Do not override this method.
     */
    public function defineDriverFormFields(FormElement $form, $context = null)
    {
        $this->defineFormFields($form, $context);

        Event::fire('system.driver.extendFields', [$this, $form, $context]);
    }

    /**
     * defineFormFields is an method for internal use to define fields used by this driver.
     * Override this method to define form fields.
     */
    public function defineFormFields(FormElement $form, $context = null)
    {
        $config = $this->getFieldConfig();

        foreach ((array) $config->fields as $name => $config) {
            $form->addFormField($name)->useConfig($config);
        }
    }

    /**
     * getHostObject returns the host object with configuration.
     * @return \Shop\Models\CurrencyRate
     */
    public function getHostObject()
    {
        return $this->model;
    }

    /**
     * getFieldConfig returns the field configuration used by this model.
     */
    public function getFieldConfig()
    {
        if ($this->fieldConfig !== null) {
            return $this->fieldConfig;
        }

        return $this->fieldConfig = $this->makeConfig($this->driverFields);
    }
}
