<?php namespace Backend\Widgets\Filter;

use Lang;
use Backend\Classes\FilterScope;
use Backend\Classes\WidgetManager;
use Backend\Classes\FilterWidgetBase;
use October\Rain\Html\Helper as HtmlHelper;
use SystemException;

/**
 * HasFilterWidgets concern
 */
trait HasFilterWidgets
{
    /**
     * @var array filterWidgets collection of all filter widgets used in this filter.
     */
    protected $filterWidgets = [];

    /**
     * @var \Backend\Classes\WidgetManager widgetManager
     */
    protected $widgetManager;

    /**
     * initFilterWidgetsConcern
     */
    protected function initFilterWidgetsConcern()
    {
        $this->widgetManager = WidgetManager::instance();
    }

    /**
     * makeFilterScopeWidget object from a filter field object
     */
    protected function makeFilterScopeWidget(FilterScope $scope): ?FilterWidgetBase
    {
        if ($scope->type !== 'widget') {
            return null;
        }

        if (isset($this->filterWidgets[$scope->scopeName])) {
            return $this->filterWidgets[$scope->scopeName];
        }

        $widgetConfig = $this->makeConfig($scope->getAttributes());
        $widgetConfig->alias = $this->alias . studly_case($this->nameToId($scope->scopeName));
        $widgetConfig->previewMode = $this->previewMode;
        $widgetConfig->model = $this->scopeModels[$scope->scopeName] ?? null;
        $widgetConfig->parentFilter = $this;

        $widgetName = $widgetConfig->widget;
        $widgetClass = $this->widgetManager->resolveFilterWidget($widgetName);

        if (!class_exists($widgetClass)) {
            throw new SystemException(Lang::get(
                'backend::lang.widget.not_registered',
                ['name' => $widgetClass]
            ));
        }

        $widget = $this->makeFilterWidget($widgetClass, $scope, $widgetConfig);

        return $this->filterWidgets[$scope->scopeName] = $widget;
    }

    /**
     * makeFilterWidget object with the supplied filter scope and widget configuration.
     * @param string $class Widget class name
     * @param mixed $scopeConfig A field name, an array of config or a FileScope object.
     * @param array $widgetConfig An array of config.
     * @return \Backend\Classes\FilterWidgetBase The widget object
     */
    public function makeFilterWidget($class, $scopeConfig = [], $widgetConfig = [])
    {
        $controller = property_exists($this, 'controller') && $this->controller
            ? $this->controller
            : $this;

        if (!class_exists($class)) {
            throw new SystemException(Lang::get('backend::lang.widget.not_registered', [
                'name' => $class
            ]));
        }

        if (is_string($scopeConfig)) {
            $scopeConfig = ['scopeName' => $scopeConfig];
        }

        if (is_array($scopeConfig)) {
            if (isset($fieldConfig['name'])) {
                $fieldConfig['scopeName'] = $fieldConfig['name'];
            }

            $filterScope = new FilterScope($scopeConfig);
            $filterScope->displayAs('widget');
        }
        else {
            $filterScope = $scopeConfig;
        }

        return new $class($controller, $filterScope, $widgetConfig);
    }

    /**
     * isFilterWidget checks if a field type is a widget or not
     */
    protected function isFilterWidget(string $scopeType): bool
    {
        if (!$scopeType) {
            return false;
        }

        if (strpos($scopeType, '\\')) {
            return true;
        }

        $widgetClass = $this->widgetManager->resolveFilterWidget($scopeType);

        if (!class_exists($widgetClass)) {
            return false;
        }

        if (is_subclass_of($widgetClass, \Backend\Classes\FilterWidgetBase::class)) {
            return true;
        }

        return false;
    }

    /**
     * getFilterWidgets for the instance
     */
    public function getFilterWidgets(): array
    {
        return $this->filterWidgets;
    }

    /**
     * getFilterWidget returns a specified filter widget
     * @param string $scope
     */
    public function getFilterWidget($scope)
    {
        if (isset($this->filterWidgets[$scope])) {
            return $this->filterWidgets[$scope];
        }

        return null;
    }

    /**
     * nameToId is a helper method to convert a field name to a valid ID attribute
     * @param $input
     * @return string
     */
    public function nameToId($input)
    {
        return HtmlHelper::nameToId($input);
    }
}
