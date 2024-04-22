<?php namespace Backend\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;

/**
 * PaletteEditor is used by the system internally on the Backend / Customize Backend page.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class PaletteEditor extends FormWidgetBase
{
    use \Backend\Models\BrandSetting\HasPalettes;

    /**
     * @var string colorModeFrom is the field name to source the color mode from
     */
    public $colorModeFrom = 'color_mode';

    /**
     * @var \Backend\Widgets\Form colorsFormWidget reference
     */
    protected $colorsFormWidget;

    const PRESET_DEFAULT = 'default';
    const PRESET_CUSTOM = 'custom';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'colorModeFrom',
        ]);

        $this->makeColorsFormWidget();
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('paletteeditor');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        $this->vars['field'] = $this->formField;
        $this->vars['presetValue'] = $this->getPresetValue();
        $this->vars['colorModeField'] = $this->getColorModeField();
        $this->vars['colorModeValue'] = $this->getColorModeValue();
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('css/paletteeditor.css');
        $this->addJs('js/paletteeditor.js');
    }

    /**
     * getSaveValue
     */
    public function getSaveValue($value)
    {
        if ($this->getColorModeValue() === 'light') {
            $lightColors = post('PaletteEditor[palette]');
            $darkColors = $this->getPaletteValue('dark');
        }
        else {
            $darkColors = post('PaletteEditor[palette]');
            $lightColors = $this->getPaletteValue('light');
        }

        // Ensure saved value are valid hex colors
        $parseAsHex = function($arr) {
            array_walk_recursive($arr, function(&$value) {
                $value = '#' . preg_replace("/[^a-zA-Z0-9]+/", '', $value);
            });
            return $arr;
        };

        return [
            'preset' => (string) $value,
            'light' => $parseAsHex((array) $lightColors),
            'dark' => $parseAsHex((array) $darkColors)
        ];
    }

    /**
     * getPresetDefinitions returns everything we know about the palette state
     */
    protected function getPresetDefinitions(): array
    {
        return array_merge(
            $this->getPaletteDefinitions(),
            [
                'custom' => [
                    'light' => $this->getLoadValue()['light'] ?? [],
                    'dark' => $this->getLoadValue()['dark'] ?? []
                ]
            ]
        );
    }

    /**
     * getPresetValue
     */
    protected function getPresetValue(): string
    {
        return $this->getLoadValue()['preset'] ?? self::PRESET_DEFAULT;
    }

    /**
     * getColorModeValue returns the color mode value (externally)
     */
    protected function getColorModeValue(): string
    {
        $colorMode = post(
            'PaletteEditor[color_mode]',
            $this->model->{$this->colorModeFrom} ?? 'light'
        );

        if ($colorMode === 'auto') {
            $colorMode = $_COOKIE['admin_color_mode_setting'] ?? 'light';
        }

        if (!in_array($colorMode, ['light', 'dark'])) {
            $colorMode = 'light';
        }

        return $colorMode;
    }

    /**
     * getPaletteValue
     */
    protected function getPaletteValue(string $mode = null): array
    {
        if ($mode === null) {
            $mode = $this->getColorModeValue();
        }

        return $this->getLoadValue()[$mode] ?? $this->getPaletteColors()[$mode];
    }

    /**
     * getColorModeField
     */
    protected function getColorModeField(): FormField
    {
        return $this->getParentForm()->getField($this->colorModeFrom);
    }

    /**
     * makeColorsFormWidget creates a form widget
     */
    protected function makeColorsFormWidget()
    {
        $config = $this->makeConfig(base_path('modules/backend/formwidgets/paletteeditor/partials/fields_colors.yaml'));

        $config->model = $this->model;
        $config->data = ['palette' => $this->getPaletteValue()];
        $config->isNested = true;

        $config->alias = $this->alias . $this->defaultAlias;
        $config->context = $this->formField->context;
        $config->arrayName = 'PaletteEditor';
        $config->sessionKey = $this->sessionKey;

        $widget = $this->makeWidget(\Backend\Widgets\Form::class, $config);
        $widget->bindToController();

        $this->colorsFormWidget = $widget;
    }
}
