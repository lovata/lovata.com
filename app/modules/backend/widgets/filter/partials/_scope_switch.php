<?php
    $activeValue = $scope->scopeValue !== null ? $scope->value : $scope->default;
?>
<div
    class="filter-scope form-check is-indeterminate"
    data-scope-name="<?= $scope->scopeName ?>">
    <input class="form-check-input" type="checkbox" id="<?= $scope->getId() ?>" data-checked="<?= $activeValue ?: '0' ?>" />
    <label class="form-check-label" for="<?= $scope->getId() ?>"><?= e($this->getHeaderValue($scope)) ?></label>
</div>
