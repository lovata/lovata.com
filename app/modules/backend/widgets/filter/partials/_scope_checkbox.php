<?php
    $activeValue = $scope->scopeValue !== null ? $scope->value : null;
?>
<div
    class="filter-scope scope-checkbox form-check"
    data-scope-name="<?= $scope->scopeName ?>">
    <input class="form-check-input" type="checkbox" id="<?= $scope->getId() ?>" <?= $activeValue ? 'checked' : '' ?> />
    <label class="form-check-label" for="<?= $scope->getId() ?>"><?= e($this->getHeaderValue($scope)) ?></label>
</div>
