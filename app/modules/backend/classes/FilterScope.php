<?php namespace Backend\Classes;

use Arr;
use Lang;
use October\Rain\Html\Helper as HtmlHelper;
use October\Rain\Element\Filter\ScopeDefinition;
use Illuminate\Support\Collection;
use SystemException;

/**
 * FilterScope is a translation of the filter scope configuration
 *
 * @method ScopeDefinition idPrefix(string $prefix) idPrefix to the field identifier so it can be totally unique.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class FilterScope extends ScopeDefinition
{
    /**
     * getOptionsFromModel looks at the model for defined options.
     */
    public function getOptionsFromModel($model, $scopeOptions)
    {
        // Method name
        if (is_string($scopeOptions)) {
            $scopeOptions = $this->getOptionsFromModelAsString($model, $scopeOptions);
        }

        // Cast collections to array
        if ($scopeOptions instanceof Collection) {
            $scopeOptions = $scopeOptions->all();
        }

        // Always be an array
        if ($scopeOptions === null) {
            return $scopeOptions = [];
        }

        return $scopeOptions;
    }

    /**
     * getOptionsFromModelAsString where options are an explicit method reference
     */
    protected function getOptionsFromModelAsString($model, string $methodName)
    {
        // Calling via ClassName::method
        if (
            strpos($methodName, '::') !== false &&
            ($staticMethod = explode('::', $methodName)) &&
            count($staticMethod) === 2 &&
            is_callable($staticMethod)
        ) {
            $scopeOptions = $staticMethod($model, $this);

            if (!is_array($scopeOptions)) {
                throw new SystemException(Lang::get('backend::lang.field.options_static_method_invalid_value', [
                    'class' => $staticMethod[0],
                    'method' => $staticMethod[1]
                ]));
            }
        }
        // Calling via $model->method
        else {
            if (!$this->objectMethodExists($model, $methodName)) {
                throw new SystemException(Lang::get('backend::lang.filter.options_method_not_exists', [
                    'model' => get_class($model),
                    'method' => $methodName,
                    'filter' => $this->fieldName
                ]));
            }

            $scopeOptions = $model->$methodName($this);
        }

        return $scopeOptions;
    }

    /**
     * applyScopeMethodToQuery
     */
    public function applyScopeMethodToQuery($query)
    {
        $methodName = $this->modelScope;

        // Calling via ClassName::method
        if (
            is_string($methodName) &&
            strpos($methodName, '::') !== false &&
            ($staticMethod = explode('::', $methodName)) &&
            count($staticMethod) === 2 &&
            is_callable($staticMethod)
        ) {
            $methodName = $staticMethod;
        }

        // Calling via query builder
        if (is_string($methodName)) {
            $query->$methodName($this);
        }
        // Calling via callable
        else {
            $methodName($query, $this);
        }
    }

    /**
     * getId returns a value suitable for the scope id property.
     */
    public function getId($suffix = null)
    {
        $id = 'scope';
        $id .= '-'.$this->scopeName;

        if ($suffix) {
            $id .= '-'.$suffix;
        }

        if ($this->idPrefix) {
            $id = $this->idPrefix . '-' . $id;
        }

        return HtmlHelper::nameToId($id);
    }

    /**
     * getDefaultScopeValue returns a fully qualified scope default value
     */
    public function getDefaultScopeValue()
    {
        $defaults = $this->defaults;
        if ($defaults === null) {
            return null;
        }

        // Basic value
        if (is_scalar($defaults)) {
            return ['value' => $defaults];
        }

        // Invalid value
        if (!is_array($defaults)) {
            return null;
        }

        // Numerical array
        if (Arr::isList($defaults)) {
            return ['value' => $defaults];
        }

        // Associative array
        return $defaults;
    }

    /**
     * objectMethodExists is an internal helper for method existence checks.
     * @param  object $object
     * @param  string $method
     */
    protected function objectMethodExists($object, $method): bool
    {
        if (method_exists($object, 'methodExists')) {
            return $object->methodExists($method);
        }

        return method_exists($object, $method);
    }
}
