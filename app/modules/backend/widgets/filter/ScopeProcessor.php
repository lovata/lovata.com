<?php namespace Backend\Widgets\Filter;

use BackendAuth;

/**
 * ScopeProcessor concern
 */
trait ScopeProcessor
{
    /**
     * processFieldOptionValues sets the callback for retrieving options
     */
    protected function processFieldOptionValues(array $scopes): void
    {
        $optionModelTypes = ['dropdown'];

        foreach ($scopes as $scope) {
            if (!in_array($scope->type, $optionModelTypes, false)) {
                continue;
            }

            // Specified explicitly on the object already
            if ($scope->hasOptions()) {
                continue;
            }

            // Defer the execution of option data collection
            $scopeOptions = $scope->optionsMethod ?: $scope->options;
            $scope->options(function () use ($scope, $scopeOptions) {
                return $scope->getOptionsFromModel($this->model, $scopeOptions);
            });
        }
    }

    /**
     * processScopeModels creates associated models for scopes
     */
    protected function processScopeModels(array $scopes): void
    {
        foreach ($scopes as $scopeName => $scope) {
            if ($className = $scope->modelClass) {
                $model = new $className;
                $this->scopeModels[$scopeName] = $model;
            }
            elseif ($this->model && $this->model->hasRelation($scopeName)) {
                $model = $this->model->makeRelation($scopeName);
                $this->scopeModels[$scopeName] = $model;
            }
        }
    }

    /**
     * processPermissionCheck check if user has permissions to show the scope
     * and removes it if permission is denied
     */
    protected function processPermissionCheck(array $scopes): void
    {
        foreach ($scopes as $scopeName => $scope) {
            if (
                $scope->permissions &&
                !BackendAuth::userHasAccess($scope->permissions, false)
            ) {
                $this->removeScope($scopeName);
            }
        }
    }

    /**
     * processFilterWidgetScopes will mutate scopes types that are registered as widgets,
     * convert their type to 'widget' and internally allocate the widget object
     */
    protected function processFilterWidgetScopes(array $scopes): void
    {
        foreach ($scopes as $scope) {
            if (!$this->isFilterWidget((string) $scope->type)) {
                continue;
            }

            $newConfig = ['widget' => $scope->type];

            if (is_array($scope->attributes)) {
                $newConfig += $scope->attributes;
            }

            $scope->useConfig($newConfig)->displayAs('widget');

            // Create filter widget instance and bind to controller
            $this->makeFilterScopeWidget($scope)->bindToController();
        }
    }

    /**
     * processLegacyDefinitions applies deprecated definitions for backwards compatibility
     */
    protected function processLegacyDefinitions(array $scopes): void
    {
        foreach ($scopes as $scope) {
            if ($scope->type === 'date') {
                $this->refitLegacyDateScope($scope);
            }
            elseif ($scope->type === 'number') {
                $this->refitLegacyNumberScope($scope);
            }
            elseif ($scope->type === 'numberrange') {
                $this->refitLegacyNumberRangeScope($scope);
            }
            elseif ($scope->type === 'daterange') {
                $this->refitLegacyDateRangeScope($scope);
            }
            elseif ($scope->type === 'text') {
                $this->refitLegacyTextScope($scope);
            }
            elseif ($scope->type === 'clear') {
                $this->refitLegacyClearScope($scope);
            }
            else {
                $this->refitLegacyDefaultScope($scope);
            }
        }
    }
}
