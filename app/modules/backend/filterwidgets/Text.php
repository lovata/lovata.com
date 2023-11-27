<?php namespace Backend\FilterWidgets;

use Db;
use DbDongle;
use Backend\Classes\FilterWidgetBase;

/**
 * Text filter
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Text extends FilterWidgetBase
{
    const CONDITION_EQUALS = 'equals';
    const CONDITION_CONTAINS = 'contains';

    /**
     * init
     */
    public function init()
    {
        $scope = $this->filterScope;

        if (!$scope->conditions) {
            $scope->conditions = [
                self::CONDITION_EQUALS => true,
                self::CONDITION_CONTAINS => true
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('text');
    }

    /**
     * renderForm the form to use for filtering
     */
    public function renderForm()
    {
        $this->prepareVars();
        return $this->makePartial('text_form');
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

        if (!$this->hasPostValue('value')) {
            return null;
        }

        return post('Filter');
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

        // Condition
        $scopeConditions = (array) $scope->conditions;
        $activeCondition = $scope->condition;
        $activeValue = $scope->value;

        // Raw SQL query
        $sqlCondition = $scopeConditions[$activeCondition] ?? null;
        if (is_string($sqlCondition)) {
            $query->whereRaw(DbDongle::parse($sqlCondition, [
                'value' => $activeValue,
            ]));
            return;
        }

        // Default query
        if ($activeCondition === self::CONDITION_CONTAINS) {
            $query->where($this->valueFrom, 'LIKE', '%'.$activeValue.'%');
            return;
        }

        $query->where($this->valueFrom, $activeValue);
    }

    /**
     * getConditionLang
     */
    public function getConditionLang($condition)
    {
        switch ($condition) {
            case self::CONDITION_EQUALS:
                return __('is equal to');

            case self::CONDITION_CONTAINS:
                return __('contains');
        }
    }
}
