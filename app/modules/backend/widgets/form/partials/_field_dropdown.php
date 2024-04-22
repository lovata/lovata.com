<?php
    $fieldOptions = $field->options();
    $useSearch = $field->getConfig('showSearch', true);
    $emptyOption = $field->getConfig('emptyOption', $field->placeholder);
    if ($emptyOption) {
        $fieldOptions = ['' => $emptyOption] + $fieldOptions;
    }
?>
<!-- Dropdown -->
<?php if ($this->previewMode || $field->readOnly): ?>
    <?php
        $fieldValue = $fieldOptions[$field->value] ?? '';
    ?>
    <div class="form-control" <?= $field->readOnly ? 'disabled' : '' ?>>
        <?php if (is_array($fieldValue)): ?>
            <?php if (Html::isValidColor($fieldValue[1])): ?>
                <span class="status-indicator" style="background:<?= $fieldValue[1] ?>"></span>
            <?php elseif (strpos($fieldValue[1], '.')): ?>
                <img src="<?= $fieldValue[1] ?>" alt="" />
            <?php else: ?>
                <i class="<?= $fieldValue[1] ?>"></i>
            <?php endif ?>
            <?= $field->getDisplayValue($fieldValue[0]) ?>
        <?php else: ?>
            <?= $field->getDisplayValue($fieldValue) ?>
        <?php endif ?>
    </div>
    <?php if ($field->readOnly): ?>
        <input
            type="hidden"
            name="<?= $field->getName() ?>"
            value="<?= $field->value ?>" />
    <?php endif ?>
<?php else: ?>
    <select
        id="<?= $field->getId() ?>"
        name="<?= $field->getName() ?>"
        class="form-control custom-select <?= $useSearch ? '' : 'select-no-search' ?>"
        <?= $field->getAttributes() ?>
        <?= $field->placeholder ? 'data-placeholder="'.e(__($field->placeholder)).'"' : '' ?>
    >
        <?php foreach ($fieldOptions as $value => $option): ?>
            <?php
                if (!is_array($option)) $option = [$option];
            ?>
            <option
                <?= $field->isSelected($value) ? 'selected="selected"' : '' ?>
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
            ><?= $field->getDisplayValue($option[0]) ?></option>
        <?php endforeach ?>
    </select>
<?php endif ?>
