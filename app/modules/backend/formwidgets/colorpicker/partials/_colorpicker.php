<?php if ($this->previewMode): ?>
    <div class="form-control"><?= e($value) ?></div>
<?php else: ?>
    <div
        id="<?= $this->getId() ?>"
        class="field-colorpicker <?= $readOnly || $disabled ? 'disabled' : '' ?>"
        data-control="colorpicker"
        <?php if ($showAlpha): ?>data-show-alpha="<?= $showAlpha ?>"<?php endif ?>
        <?php if ($allowEmpty): ?>data-allow-empty="<?= $allowEmpty ?>"<?php endif ?>
        data-data-locker="#<?= $this->getId('input') ?>"
        <?php if ($readOnly || $disabled): ?>data-disabled="true"<?php endif ?>
        <?= $this->formField->getAttributes() ?>>

        <?php if ($showInput): ?>
            <?= $this->makePartial('mode_input') ?>
        <?php else: ?>
            <?= $this->makePartial('mode_preset') ?>
        <?php endif ?>
    </div>
<?php endif ?>
