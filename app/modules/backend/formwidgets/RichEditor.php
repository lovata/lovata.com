<?php namespace Backend\FormWidgets;

use App;
use File;
use Request;
use BackendAuth;
use Backend\Classes\FormWidgetBase;
use Backend\Models\EditorSetting;

/**
 * RichEditor renders a rich content editor field.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class RichEditor extends FormWidgetBase
{
    //
    // Configurable Properties
    //

    /**
     * @var bool Determines whether content has HEAD and HTML tags.
     */
    public $fullPage = false;

    /**
     * @var bool inlineMode uses line breaks instead of paragraph wrappers for each new line.
     */
    public $inlineMode = false;

    /**
     * @var bool Determines whether content has HEAD and HTML tags.
     */
    public $toolbarButtons;

    /**
     * @var bool If true, the editor is set to read-only mode
     */
    public $readOnly = false;

    /**
     * @var bool The Legacy mode disables the Vue integration.
     */
    public $legacyMode = false;

    /**
     * @var bool showMargins includes resizable document margins.
     * Only works in Vue applications and form document layouts.
     */
    public $showMargins = false;

    /**
     * @var string Defines a mount point for the editor toolbar.
     * Must include a module name that exports the Vue application and a state element name.
     * Format: module.name::stateElementName
     * Only works in Vue applications and form document layouts.
     */
    public $externalToolbarAppState = null;

    /**
     * @var string Defines an event bus for an external toolbar.
     * Must include a module name that exports the Vue application and a state element name.
     * Format: module.name::eventBus
     * Only works in Vue applications and form document layouts.
     */
    public $externalToolbarEventBus = null;

    //
    // Object Properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'richeditor';

    /**
     * @inheritDoc
     */
    public function init()
    {
        if ($this->formField->disabled) {
            $this->readOnly = true;
        }

        $this->fillFromConfig([
            'fullPage',
            'inlineMode',
            'readOnly',
            'toolbarButtons',
            'legacyMode',
            'showMargins',
            'externalToolbarAppState',
            'externalToolbarEventBus'
        ]);

        if (!$this->legacyMode) {
            $this->controller->registerVueComponent(\Backend\VueComponents\RichEditorDocumentConnector::class);
        }
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('richeditor');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        $this->vars['field'] = $this->formField;
        $this->vars['editorLang'] = $this->getValidEditorLang();
        $this->vars['fullPage'] = $this->fullPage;
        $this->vars['inlineMode'] = $this->inlineMode;
        $this->vars['stretch'] = $this->formField->stretch;
        $this->vars['size'] = $this->formField->size;
        $this->vars['readOnly'] = $this->readOnly;
        $this->vars['showMargins'] = $this->showMargins;
        $this->vars['externalToolbarAppState'] = $this->externalToolbarAppState;
        $this->vars['externalToolbarEventBus'] = $this->externalToolbarEventBus;
        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['toolbarButtons'] = $this->evalToolbarButtons();
        $this->vars['useMediaManager'] = BackendAuth::userHasAccess('media.library');
        $this->vars['legacyMode'] = $this->legacyMode;

        $this->vars['globalToolbarButtons'] = EditorSetting::getConfigured('html_toolbar_buttons');
        $this->vars['allowEmptyTags'] = EditorSetting::getConfigured('html_allow_empty_tags');
        $this->vars['allowTags'] = EditorSetting::getConfigured('html_allow_tags');
        $this->vars['allowAttrs'] = EditorSetting::getConfigured('html_allow_attrs');
        $this->vars['noWrapTags'] = EditorSetting::getConfigured('html_no_wrap_tags');
        $this->vars['removeTags'] = EditorSetting::getConfigured('html_remove_tags');
        $this->vars['lineBreakerTags'] = EditorSetting::getConfigured('html_line_breaker_tags');

        $this->vars['imageStyles'] = EditorSetting::getConfiguredStyles('html_style_image');
        $this->vars['linkStyles'] = EditorSetting::getConfiguredStyles('html_style_link');
        $this->vars['paragraphStyles'] = EditorSetting::getConfiguredStyles('html_style_paragraph');
        $this->vars['paragraphFormats'] = EditorSetting::getConfiguredFormats('html_paragraph_formats');
        $this->vars['tableStyles'] = EditorSetting::getConfiguredStyles('html_style_table');
        $this->vars['tableCellStyles'] = EditorSetting::getConfiguredStyles('html_style_table_cell');

        $this->vars['isAjax'] = Request::ajax();
    }

    /**
     * evalToolbarButtons to use based on config.
     * @return string
     */
    protected function evalToolbarButtons()
    {
        $buttons = $this->toolbarButtons;

        if (is_string($buttons)) {
            $buttons = array_map(function ($button) {
                return strlen($button) ? $button : '|';
            }, explode('|', $buttons));
        }

        return $buttons;
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('css/richeditor.css');
        $this->addJs('js/build-min.js');
        $this->addJs('js/richeditor.js');
        $this->addJs('/modules/backend/formwidgets/codeeditor/assets/js/build-min.js');
    }

    /**
     * getValidEditorLang returns a proposed language code for Froala.
     * @return string|null
     */
    protected function getValidEditorLang()
    {
        $locale = App::getLocale();

        // English is baked in
        if ($locale !== 'en') {
            return str_replace('-', '_', strtolower($locale));
        }

        return null;
    }
}
