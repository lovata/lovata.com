<?php namespace Cms\FormWidgets;

use Cms\Models\PageLookupItem;
use Backend\Classes\FormWidgetBase;

/**
 * PageFinder renders a page finder field.
 *
 *    page:
 *        label: Featured Page
 *        type: pagefinder
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class PageFinder extends FormWidgetBase
{
    use \Backend\Traits\SearchableWidget;

    //
    // Configurable Properties
    //

    /**
     * @var bool singleMode only allows items to be selected that resovle to a single URL.
     */
    public $singleMode = false;

    /**
     * @var bool showReference disables link resolution when displaying the selection for performance reasons.
     */
    public $showReference = false;

    //
    // Object Properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'pagefinder';

    /**
     * @var \Backend\Classes\WidgetBase selectWidget reference to the widget used for selecting a page.
     */
    protected $selectWidget;

    /**
     * @var PageLookupItem lookupItem
     */
    protected $lookupItem;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'singleMode',
            'showReference',
        ]);

        if ($this->formField->disabled || $this->formField->readOnly) {
            $this->previewMode = true;
        }
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('css/pagefinder.css');
        $this->addJs('js/pagefinder.js');
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('container');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['field'] = $this->formField;
        $this->vars['nameValue'] = $this->getNameValue();
        $this->vars['descriptionValue'] = $this->getDescriptionValue();
        $this->vars['singleMode'] = $this->singleMode;
    }

    /**
     * onRefresh AJAX handler
     */
    public function onRefresh()
    {
        $value = post($this->getFieldName());

        $this->setKeyValue($value);

        $this->prepareVars();

        return ['#'.$this->getId('container') => $this->makePartial('pagefinder')];
    }

    /**
     * onClearRecord AJAX handler
     */
    public function onClearRecord()
    {
        $this->setKeyValue('');

        $this->prepareVars();

        return ['#'.$this->getId('container') => $this->makePartial('pagefinder')];
    }

    /**
     * getNameValue
     */
    public function getNameValue()
    {
        if ($this->showReference) {
            return $this->getLoadValue();
        }

        $reference = $this->getLookupItemValue();
        if (!$reference) {
            return '';
        }

        return $reference->title ?: $reference->getTypeLabel();
    }

    /**
     * getDescriptionValue
     */
    public function getDescriptionValue()
    {
        if ($this->showReference) {
            return '';
        }

        return $this->getLookupItemValue()->url ?? '';
    }

    /**
     * getLookupItemValue
     */
    protected function getLookupItemValue()
    {
        if ($this->lookupItem !== null) {
            return $this->lookupItem;
        }

        return $this->lookupItem = PageLookupItem::resolveFromSchema((string) $this->getLoadValue());
    }

    /**
     * setKeyValue
     */
    public function setKeyValue($value)
    {
        $this->formField->value = $value;
    }
}
