<?php
    $activeValue = $scope->scopeValue !== null ? $scope->value : $scope->default;
    $scopeOptions = $scope->options();
    if ($emptyOption = $scope->emptyOption) {
        $scopeOptions = ['' => $emptyOption] + $scopeOptions;
    }
?>
<div
    class="filter-scope scope-dropdown"
    data-scope-name="<?= $scope->scopeName ?>">
    <select
        id="<?= $scope->getId() ?>"
        class="select custom-select select-no-search select-dropdown-auto-width"
        style="opacity:0"
    >
        <?php foreach ($scopeOptions as $value => $option): ?>
            <?php
                if (!is_array($option)) $option = [$option];
            ?>
            <option
                <?= (string) $activeValue === (string) $value ? 'selected="selected"' : '' ?>
                <?php if (isset($option[1])): ?>
                    <?php if (Html::isValidColor($option[1])): ?>
                        data-status="<?= $option[1] ?>"
                    <?php elseif (strpos($option[1], '.')): ?>
                        data-image="<?= $option[1] ?>"
                    <?php else: ?>
                        data-icon="<?= $option[1] ?>"
                    <?php endif ?>
                <?php endif ?>
                value="<?= e($value) ?>"
            ><?= e(__($option[0])) ?></option>
        <?php endforeach ?>
    </select>
</div>
