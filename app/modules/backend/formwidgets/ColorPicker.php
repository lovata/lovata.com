<?php namespace Backend\FormWidgets;

use Lang;
use Backend\Classes\FormWidgetBase;
use ApplicationException;

/**
 * ColorPicker renders a color picker field.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ColorPicker extends FormWidgetBase
{
    //
    // Configurable Properties
    //

    /**
     * @var array availableColors by default
     */
    public $availableColors = [
        '#1abc9c', '#16a085',
        '#2ecc71', '#27ae60',
        '#3498db', '#2980b9',
        '#9b59b6', '#8e44ad',
        '#34495e', '#2b3e50',
        '#f1c40f', '#f39c12',
        '#e67e22', '#d35400',
        '#e74c3c', '#c0392b',
        '#ecf0f1', '#bdc3c7',
        '#95a5a6', '#7f8c8d',
    ];

    /**
     * @var bool allowEmpty value
     */
    public $allowEmpty = false;

    /**
     * @var bool allowCustom value
     */
    public $allowCustom = true;

    /**
     * @var bool showAlpha opacity slider
     */
    public $showAlpha = false;

    /**
     * @var bool|null showInput displays an input and disables the available colors.
     */
    public $showInput;

    /**
     * @var bool readOnly if true, the color picker is set to read-only mode
     */
    public $readOnly = false;

    /**
     * @var bool disabled if true, the color picker is set to disabled mode
     */
    public $disabled = false;

    //
    // Object Properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'colorpicker';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'availableColors',
            'allowCustom',
            'allowEmpty',
            'showInput',
            'showAlpha',
            'readOnly',
            'disabled',
        ]);

        // @deprecated remove default colors with showInput true as default (v4)
        // when colors provided, even as empty array, showInput becomes false -sg
        if ($this->availableColors === false && $this->showInput === null) {
            $this->showInput = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('colorpicker');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $value = $this->getLoadValue();
        $this->vars['availableColors'] = $availableColors = $this->getAvailableColors();
        $this->vars['allowCustom'] = $this->allowCustom;
        $this->vars['allowEmpty'] = $this->allowEmpty;
        $this->vars['showAlpha'] = $this->showAlpha;
        $this->vars['showInput'] = $this->showInput;
        $this->vars['readOnly'] = $this->readOnly;
        $this->vars['disabled'] = $this->disabled;
        $this->vars['isCustomColor'] = !in_array($value, $availableColors);
    }

    /**
     * getAvailableColors as a list of available colors.
     */
    protected function getAvailableColors(): array
    {
        $availableColors = $this->availableColors;
        if (is_array($availableColors)) {
            return $availableColors;
        }

        if (is_string($availableColors) && strlen($availableColors)) {
            if (!$this->model->methodExists($availableColors)) {
                throw new ApplicationException(Lang::get('backend::lang.field.colors_method_not_exists', [
                    'model' => get_class($this->model),
                    'method' => $availableColors,
                    'field' => $this->formField->fieldName
                ]));
            }

            // @deprecated just pass form field here -sg
            return (array) $this->model->{$availableColors}(
                $this->formField->fieldName,
                $this->formField->value,
                $this->formField->config
            );
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('vendor/spectrum/spectrum.css');
        $this->addJs('vendor/spectrum/spectrum.js');
        $this->addCss('css/colorpicker.css');
        $this->addJs('js/colorpicker.js');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        if (!strlen($value)) {
            return null;
        }

        return $this->parseAsHex($value);
    }

    /**
     * parseAsHex ensures saved value is a valid hex color
     */
    protected function parseAsHex($value)
    {
        return '#' . preg_replace("/[^a-zA-Z0-9]+/", '', $value);
    }
}
