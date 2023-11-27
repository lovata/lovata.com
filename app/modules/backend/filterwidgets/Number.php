<?php namespace Backend\FilterWidgets;

use Db;
use DbDongle;
use Backend\Classes\FilterWidgetBase;

/**
 * Number filter
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Number extends FilterWidgetBase
{
    const CONDITION_EQUALS = 'equals';
    const CONDITION_BETWEEN = 'between';
    const CONDITION_GREATER = 'greater';
    const CONDITION_LESSER = 'lesser';

    /**
     * init
     */
    public function init()
    {
        $scope = $this->filterScope;

        if (!$scope->conditions) {
            $scope->conditions = [
                self::CONDITION_EQUALS => true,
                self::CONDITION_BETWEEN => true,
                self::CONDITION_GREATER => true,
                self::CONDITION_LESSER => true
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('number');
    }

    /**
     * renderForm the form to use for filtering
     */
    public function renderForm()
    {
        $this->prepareVars();
        return $this->makePartial('number_form');
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
        $condition = post('Filter[condition]');

        if ($condition === self::CONDITION_BETWEEN) {
            if (!$this->hasPostValue('min') && !$this->hasPostValue('max')) {
                return null;
            }

            if (!$this->hasPostValue('min')) {
                $value['value'] = post('Filter[max]');
                $value['condition'] = self::CONDITION_LESSER;
            }
            elseif (!$this->hasPostValue('max')) {
                $value['value'] = post('Filter[min]');
                $value['condition'] = self::CONDITION_GREATER;
            }
        }
        else {
            if (!$this->hasPostValue('value')) {
                return null;
            }
        }

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

        // Condition
        $scopeConditions = (array) $scope->conditions;
        $activeCondition = $scope->condition;

        // Raw SQL query
        $sqlCondition = $scopeConditions[$activeCondition] ?? null;
        if (is_string($sqlCondition)) {
            $query->whereRaw(DbDongle::parse(strtr($sqlCondition, [
                ':filtered' => $this->parseNumber($scope->value),
                ':value' => $this->parseNumber($scope->value),
                ':min' => $this->parseNumber($scope->min),
                ':max' => $this->parseNumber($scope->max),
            ])));
            return;
        }

        // Default query
        if ($activeCondition === self::CONDITION_EQUALS) {
            $query->where($this->valueFrom, $scope->value);
            return;
        }

        if ($activeCondition === self::CONDITION_BETWEEN) {
            $query
                ->where($this->valueFrom, '>=', $scope->min)
                ->where($this->valueFrom, '<=', $scope->max);
            return;
        }

        if ($activeCondition === self::CONDITION_GREATER) {
            $query->where($this->valueFrom, '>=', $scope->value);
            return;
        }

        if ($activeCondition === self::CONDITION_LESSER) {
            $query->where($this->valueFrom, '<=', $scope->value);
            return;
        }
    }

    /**
     * parseNumber for SQL
     */
    public function parseNumber($value, array $options = [])
    {
        if (!is_numeric($value)) {
            return '';
        }

        // Arithmetic operator
        return +$value;
    }

    /**
     * getConditionLang
     */
    public function getConditionLang($condition)
    {
        switch ($condition) {
            case self::CONDITION_EQUALS:
                return __('is equal to');

            case self::CONDITION_BETWEEN:
                return __('is between');

            case self::CONDITION_GREATER:
                return __('is greater than');

            case self::CONDITION_LESSER:
                return __('is less than');
        }
    }
}
