<?php namespace Backend\Widgets;

use Backend\Classes\WidgetBase;

/**
 * Toolbar Widget
 * Used for building a toolbar, renders a toolbar.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Toolbar extends WidgetBase
{
    //
    // Configurable Properties
    //

    /**
     * @var string buttons partial name
     */
    public $buttons;

    /**
     * @var array|string search widget configuration or partial name, optional.
     */
    public $search;

    //
    // Object Properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'toolbar';

    /**
     * @var WidgetBase searchWidget reference
     */
    protected $searchWidget;

    /**
     * @var array cssClasses to apply to the toolbar container element
     */
    public $cssClasses = [];

    /**
     * @var string listWidgetId
     */
    public $listWidgetId;

    /**
     * init the widget, called by the constructor and free from its parameters.
     */
    public function init()
    {
        $this->fillFromConfig([
            'buttons',
            'search',
        ]);

        // Prepare the search widget (optional)
        if (isset($this->search)) {
            if (is_string($this->search)) {
                $searchConfig = $this->makeConfig(['partial' => $this->search]);
            }
            else {
                $searchConfig = $this->makeConfig($this->search);
            }

            $searchConfig->alias = $this->alias . 'Search';
            $this->searchWidget = $this->makeWidget(\Backend\Widgets\Search::class, $searchConfig);
            $this->searchWidget->bindToController();
        }
    }

    /**
     * Renders the widget.
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('toolbar');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        $this->vars['search'] = $this->searchWidget ? $this->searchWidget->render() : '';
        $this->vars['cssClasses'] = implode(' ', $this->cssClasses);
        $this->vars['controlPanel'] = $this->makeControlPanel();
    }

    /**
     * getSearchWidget
     */
    public function getSearchWidget()
    {
        return $this->searchWidget;
    }

    /**
     * makeControlPanel
     */
    public function makeControlPanel()
    {
        if (!isset($this->buttons)) {
            return '<div data-control="toolbar"></div>';
        }

        return $this->controller->makePartial($this->buttons, $this->vars);
    }
}
