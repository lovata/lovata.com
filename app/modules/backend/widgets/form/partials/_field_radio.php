<?php
    $fieldOptions = $field->options();
    $inlineOptions = $field->inlineOptions;
?>
<!-- Radio List -->
<?php if (count($fieldOptions)): ?>

    <?php $index = 0; foreach ($fieldOptions as $value => $option): ?>
        <?php
            $index++;
            if (is_string($option))
                $option = array($option);

            $fieldId = md5(uniqid($field->getId($index), true));
        ?>
        <div class="form-check <?= $inlineOptions ? 'form-check-inline' : '' ?>">
            <input
                class="form-check-input"
                id="<?= $fieldId ?>"
                name="<?= $field->getName() ?>"
                value="<?= e($value) ?>"
                type="radio"
                <?= $field->isSelected($value) ? 'checked' : '' ?>
                <?= $this->previewMode ? 'disabled' : '' ?>
                <?= $field->getAttributes() ?>
            />

            <label class="form-check-label" for="<?= $fieldId ?>">
                <?= $field->getDisplayValue($option[0]) ?>
            </label>
            <?php if (isset($option[1])): ?>
                <p class="form-text"><?= $field->getDisplayValue($option[1]) ?></p>
            <?php endif ?>
        </div>
    <?php endforeach ?>

<?php else: ?>

    <!-- No options specified -->
    <?php if ($field->placeholder): ?>
        <p><?= e(__($field->placeholder)) ?></p>
    <?php endif ?>

<?php endif ?>
