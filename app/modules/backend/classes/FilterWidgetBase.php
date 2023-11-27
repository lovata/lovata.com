<?php namespace Backend\Classes;

use Backend\Classes\FilterScope;

/**
 * FilterWidgetBase class contains widgets used specifically for filters
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
abstract class FilterWidgetBase extends WidgetBase
{
    /**
     * @var \October\Rain\Database\Model model object for the filter.
     */
    public $model;

    /**
     * @var FilterScope filterScope object containing general filter scope information.
     */
    protected $filterScope;

    /**
     * @var string scopeName
     */
    protected $scopeName;

    /**
     * @var string valueFrom
     */
    protected $valueFrom;

    /**
     * @var Backend\Widgets\Filter parentFilter that contains this scope
     */
    protected $parentFilter = null;

    /**
     * __construct
     * @param Controller $controller
     * @param FilterScope $filterScope
     * @param array $configuration
     */
    public function __construct($controller, $filterScope, $configuration = [])
    {
        $this->filterScope = $filterScope;
        $this->scopeName = $filterScope->scopeName;
        $this->valueFrom = $filterScope->valueFrom ?: $this->scopeName;
        $this->config = $this->makeConfig($configuration);

        $this->fillFromConfig([
            'model',
            'parentFilter',
        ]);

        parent::__construct($controller, $configuration);
    }

    /**
     * getParentFilter retrieves the parent form for this formwidget
     * @return Backend\Widgets\Filter|null
     */
    public function getParentFilter()
    {
        return $this->parentFilter;
    }

    /**
     * renderForm the form to use for filtering
     */
    public function renderForm()
    {
    }

    /**
     * getScopeName
     */
    public function getScopeName()
    {
        return $this->filterScope->scopeName;
    }

    /**
     * getLoadValue
     */
    public function getLoadValue()
    {
        return $this->filterScope->scopeValue;
    }

    /**
     * getHeaderValue looks up the scope header
     */
    public function getHeaderValue()
    {
        return $this->getParentFilter()->getHeaderValue($this->filterScope);
    }

    /**
     * getActiveValue
     */
    public function getActiveValue()
    {
        if (post('clearScope')) {
            return null;
        }

        return post('Filter');
    }

    /**
     * getFilterScope
     */
    public function getFilterScope()
    {
        return $this->filterScope;
    }

    /**
     * applyScopeToQuery
     */
    public function applyScopeToQuery($query)
    {
    }

    /**
     * hasPostValue
     */
    protected function hasPostValue($name): bool
    {
        return strlen(trim(post("Filter[{$name}]"))) > 0;
    }
}
