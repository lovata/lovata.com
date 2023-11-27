<?php namespace Backend\Widgets;

use Lang;
use Throwable;
use Backend\Classes\WidgetBase;

/**
 * Search Widget
 * Used for building a toolbar, Renders a search container.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Search extends WidgetBase
{
    //
    // Configurable Properties
    //

    /**
     * @var string prompt is the search placeholder text.
     */
    public $prompt;

    /**
     * @var bool growable when selected.
     */
    public $growable = true;

    /**
     * @var string partial custom partial file definition, in context of the controller.
     */
    public $partial;

    /**
     * @var string mode defines the search mode. Commonly passed to the searchWhere() query.
     */
    public $mode;

    /**
     * @var string scope custom method name. Commonly passed to the query.
     */
    public $scope;

    /**
     * @var bool searchOnEnter searches on enter key instead of every key stroke.
     */
    public $searchOnEnter = false;

    //
    // Object Properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'search';

    /**
     * @var string activeTerm pulled from session data.
     */
    protected $activeTerm;

    /**
     * @var array cssClasses to apply to the list container element.
     */
    public $cssClasses = [];

    /**
     * init the widget, called by the constructor and free from its parameters.
     */
    public function init()
    {
        $this->fillFromConfig([
            'prompt',
            'partial',
            'growable',
            'scope',
            'mode',
            'searchOnEnter',
        ]);

        if ($this->growable) {
            $this->cssClasses[] = 'growable';
        }
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        // @deprecated use controls\searchinput\searchinput.js
        // update html to use searchinput instead of searchwidget
        $this->addJs('js/october.search.js');
    }

    /**
     * render the widget
     */
    public function render()
    {
        $this->prepareVars();

        if ($this->partial) {
            return $this->controller->makePartial($this->partial);
        }

        return $this->makePartial('search');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        $this->vars['cssClasses'] = implode(' ', $this->cssClasses);
        $this->vars['placeholder'] = Lang::get($this->prompt);
        $this->vars['value'] = $this->getActiveTerm();
        $this->vars['searchOnEnter'] = $this->searchOnEnter;
    }

    /**
     * onSubmit search field
     */
    public function onSubmit()
    {
        // Save or reset search term in session
        $this->setActiveTerm(post($this->getName()));

        // Trigger class event, merge results as viewable array
        $params = func_get_args();
        try {
            $result = $this->fireEvent('search.submit', [$params]);
        }
        catch (Throwable $e) {
            $this->setActiveTerm('');
            throw $e;
        }

        if ($result && is_array($result)) {
            return call_user_func_array('array_merge', $result);
        }
    }

    /**
     * getActiveTerm returns an active search term for this widget instance.
     */
    public function getActiveTerm()
    {
        return $this->activeTerm = $this->getSession('term', '');
    }

    /**
     * setActiveTerm for this widget instance.
     */
    public function setActiveTerm($term)
    {
        if (!is_string($term) || !strlen($term)) {
            $this->resetSession();
        }
        else {
            $this->putSession('term', $term);
        }

        $this->activeTerm = $term;
    }

    /**
     * getName returns a value suitable for the field name property.
     * @return string
     */
    public function getName()
    {
        return $this->alias . '[term]';
    }
}
