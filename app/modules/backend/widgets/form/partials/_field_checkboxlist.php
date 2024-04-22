<?php
    $fieldOptions = $field->options();
    $checkedValues = (array) $field->value;
    $isScrollable = count($fieldOptions) > 10;
    $inlineOptions = $field->inlineOptions && !$isScrollable;
    $isQuickselect = $field->getConfig('quickselect', $isScrollable);
?>
<!-- Checkbox List -->
<?php if ($this->previewMode): ?>

    <?php if ($field->value): ?>
        <div class="field-checkboxlist control-disabled">
            <?php $index = 0; foreach ($fieldOptions as $value => $option): ?>
                <?php
                    $index++;
                    $checkboxId = 'checkbox_'.$field->getId().'_'.$index;
                    if (!in_array($value, $checkedValues)) continue;
                    if (!is_array($option)) $option = [$option];
                ?>
                <div class="form-check <?= $inlineOptions ? 'form-check-inline' : '' ?>">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="<?= $checkboxId ?>"
                        name="<?= $field->getName() ?>[]"
                        value="<?= e($value) ?>"
                        disabled
                        checked
                    />
                    <label class="form-check-label" for="<?= $checkboxId ?>">
                        <?= $field->getDisplayValue($option[0]) ?>
                    </label>
                    <?php if (isset($option[1])): ?>
                        <p class="form-text"><?= $field->getDisplayValue($option[1]) ?></p>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php else: ?>
        <!-- No options specified -->
        <?php if ($field->placeholder): ?>
            <p><?= e(__($field->placeholder)) ?></p>
        <?php endif ?>
    <?php endif ?>

<?php else: ?>

    <div class="field-checkboxlist <?= $isScrollable ? 'is-scrollable' : '' ?> <?= $field->disabled ? 'control-disabled' : '' ?>" <?= $field->getAttributes() ?>>
        <?php if ($isQuickselect): ?>
            <!-- Quick selection -->
            <div class="checkboxlist-controls">
                <a
                    href="javascript:;" class="backend-toolbar-button control-button"
                    <?= $field->disabled ? 'disabled' : '' ?>
                    <?= $field->readOnly ? 'readonly' : '' ?>
                    data-field-checkboxlist-all>
                    <i class="icon-check-multi"></i>
                    <span class="button-label"><?= e(trans('backend::lang.form.select_all')) ?></span>
                </a>

                <a
                    href="javascript:;" class="backend-toolbar-button control-button"
                    <?= $field->disabled ? 'disabled' : '' ?>
                    <?= $field->readOnly ? 'readonly' : '' ?>
                    data-field-checkboxlist-none>
                    <i class="icon-eraser"></i>
                    <span class="button-label"><?= e(trans('backend::lang.form.select_none')) ?></span>
                </a>
            </div>
        <?php endif ?>

        <div class="field-checkboxlist-inner">
            <?php if ($isScrollable): ?>
                <!-- Scrollable Checkbox list -->
                <div class="field-checkboxlist-scrollable">
                    <div class="control-scrollbar" data-control="scrollbar">
            <?php endif ?>

            <input
                type="hidden"
                name="<?= $field->getName() ?>"
                value=""
                <?= $field->disabled ? 'disabled' : '' ?>
            />

            <?php if (count($fieldOptions)): ?>
                <?php $index = 0; foreach ($fieldOptions as $value => $option): ?>
                    <?php
                        $index++;
                        $checkboxId = 'checkbox_'.$field->getId().'_'.$index;
                        if (!is_array($option)) $option = [$option];
                    ?>
                    <div class="form-check <?= $inlineOptions ? 'form-check-inline' : '' ?>">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="<?= $checkboxId ?>"
                            name="<?= $field->getName() ?>[]"
                            value="<?= e($value) ?>"
                            <?= in_array($value, $checkedValues) ? 'checked="checked"' : '' ?>
                            <?= $field->disabled ? 'disabled' : '' ?>
                            <?= $field->readOnly ? 'onclick="return false"' : '' ?>
                        />
                        <label class="form-check-label" for="<?= $checkboxId ?>">
                            <?= $field->getDisplayValue($option[0]) ?>
                        </label>
                        <?php if (isset($option[1]) && strlen($option[1])): ?>
                            <p class="form-text"><?= $field->getDisplayValue($option[1]) ?></p>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            <?php else: ?>
                <p class="text-muted"><?= e(__($field->emptyOption)) ?></p>
            <?php endif ?>

            <?php if ($isScrollable): ?>
                    </div>
                </div>
            <?php endif ?>

        </div>
    </div>

<?php endif ?>
