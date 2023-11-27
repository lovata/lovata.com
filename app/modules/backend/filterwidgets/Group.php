<?php namespace Backend\FilterWidgets;

use Db;
use Str;
use Lang;
use DbDongle;
use Backend\Classes\FilterWidgetBase;
use October\Rain\Element\ElementHolder;
use ApplicationException;

/**
 * Group filter
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Group extends FilterWidgetBase
{
    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addJs('js/groupfilter.js');
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('group');
    }

    /**
     * renderForm the form to use for filtering
     */
    public function renderForm()
    {
        $this->prepareVars();
        return $this->makePartial('group_form');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        $this->vars['scope'] = $this->filterScope;
    }

    /**
     * getActiveValue
     */
    public function getActiveValue()
    {
        if (post('clearScope')) {
            return null;
        }

        $value = post('Filter');
        if (!$this->hasPostValue('value')) {
            return null;
        }

        $value['value'] = json_decode($value['value'], true);
        if (!$value['value']) {
            return null;
        }

        // @deprecated this is to keep support with v1 API where values were inside the keys
        $value['value'] = array_combine($value['value'], $value['value']);

        return $value;
    }

    /**
     * applyScopeToQuery
     */
    public function applyScopeToQuery($query)
    {
        $scope = $this->filterScope;

        // Scope
        if ($scope->modelScope) {
            $scope->applyScopeMethodToQuery($query);
            return;
        }

        // Active value
        $activeValue = (array) $scope->value;
        if (!count($activeValue)) {
            return;
        }

        // Raw SQL query
        $sqlCondition = $scope->conditions;
        if (is_string($sqlCondition)) {
            $filtered = implode(',', array_build($activeValue, function ($key, $_value) {
                return [$key, Db::getPdo()->quote($_value)];
            }));

            $query->whereRaw(DbDongle::parse(strtr($sqlCondition, [
                ':filtered' => $filtered,
                ':value' => $filtered,
            ])));
            return;
        }

        // Default query
        if ($this->model) {
            $query->whereHas($this->valueFrom, function($q) use ($activeValue) {
                $q->whereIn('id', $activeValue);
            });
        }
        else {
            $query->whereIn($this->valueFrom, $activeValue);
        }
    }

    /**
     * onGetGroupOptions
     */
    public function onGetGroupOptions()
    {
        $scope = $this->filterScope;
        $searchQuery = post('search');

        $available = $this->getAvailableOptions($searchQuery);
        $active = $searchQuery ? [] : $this->filterActiveOptions((array) $scope->value, $available);

        return [
            'options' => [
                'available' => $this->optionsToAjax($available),
                'active' => $this->optionsToAjax($active),
            ]
        ];
    }

    /**
     * getAvailableOptions returns the available options a scope can use, either from the
     * model relation or from a supplied array. Optionally apply a search constraint
     * to the options
     */
    protected function getAvailableOptions(string $searchQuery = null): array
    {
        $available = [];
        $scope = $this->filterScope;

        if ($scope->options) {
            $available = $this->getOptionsFromArray($searchQuery);
        }
        else {
            $nameColumn = $scope->nameFrom;
            $options = $this->getOptionsFromModel($searchQuery);

            foreach ($options as $option) {
                $available[$option->getKey()] = $option->{$nameColumn};
            }
        }

        if ($scope->emptyOption) {
            $available = ['' => Lang::get($scope->emptyOption)] + $available;
        }

        return $available;
    }

    /**
     * filterActiveOptions removes any already selected options from the available options,
     * returns a newly built array
     */
    protected function filterActiveOptions(array $activeKeys, array $availableOptions): array
    {
        $active = [];
        foreach ($availableOptions as $id => $option) {
            if (!in_array($id, $activeKeys)) {
                continue;
            }

            $active[$id] = $option;
        }

        return $active;
    }

    /**
     * getOptionsFromModel looks at the model for defined scope items.
     * @return Collection
     */
    protected function getOptionsFromModel($searchQuery = null)
    {
        $scope = $this->filterScope;
        $model = $this->model;
        $query = $model->newQuery();
        $query->limit(200);

        // Extensibility
        $this->getParentFilter()->extendScopeModelQuery($scope, $query);

        if (!$searchQuery) {
            // If scope has active filter(s) run additional query and merge it with base query
            if ($scope->value) {
                $modelIds = array_keys((array) $scope->value);
                $activeOptions = $model->newQuery()->findMany($modelIds);
            }

            $modelOptions = isset($activeOptions)
                ? $query->get()->merge($activeOptions)
                : $query->get();

            return $modelOptions;
        }

        $searchFields = [$model->getKeyName(), $scope->nameFrom];

        return $query->searchWhere($searchQuery, $searchFields)->get();
    }

    /**
     * getOptionsFromArray looks at the defined set of options for scope items, or the model method.
     * @return array
     */
    protected function getOptionsFromArray($searchQuery = null)
    {
        // Load the data
        $scope = $this->filterScope;
        $options = $scope->optionsMethod ?: $scope->options;

        if (is_scalar($options)) {
            $model = $this->model;
            $methodName = $options;

            if (!$model->methodExists($methodName)) {
                throw new ApplicationException(Lang::get('backend::lang.filter.options_method_not_exists', [
                    'model'  => get_class($model),
                    'method' => $methodName,
                    'filter' => $scope->scopeName
                ]));
            }

            // For passing to events
            $holder = new ElementHolder($this->getParentFilter()->getScopes());
            $options = $model->$methodName($holder);
        }
        elseif (!is_array($options)) {
            $options = [];
        }

        // Apply the search
        $searchQuery = Str::lower($searchQuery);
        if (strlen($searchQuery)) {
            $options = $this->filterOptionsBySearch($options, $searchQuery);
        }

        return $options;
    }

    /**
     * filterOptionsBySearch filters an array of options by a search term.
     * @param array $options
     * @param string $query
     * @return array
     */
    protected function filterOptionsBySearch($options, $query)
    {
        $filteredOptions = [];

        $optionMatchesSearch = function ($words, $option) {
            foreach ($words as $word) {
                $word = trim($word);
                if (!strlen($word)) {
                    continue;
                }

                if (!Str::contains(Str::lower($option), $word)) {
                    return false;
                }
            }

            return true;
        };

        // Exact
        foreach ($options as $index => $option) {
            if (Str::is(Str::lower($option), $query)) {
                $filteredOptions[$index] = $option;
                unset($options[$index]);
            }
        }

        // Fuzzy
        $words = explode(' ', $query);
        foreach ($options as $index => $option) {
            if ($optionMatchesSearch($words, $option)) {
                $filteredOptions[$index] = $option;
            }
        }

        return $filteredOptions;
    }

    /**
     * optionsToAjax converts a key/pair array to a named array {id: 1, name: 'Foobar'}
     */
    protected function optionsToAjax(array $options): array
    {
        $processed = [];

        foreach ($options as $id => $result) {
            $processed[] = ['id' => $id, 'name' => trans($result)];
        }

        return $processed;
    }
}
