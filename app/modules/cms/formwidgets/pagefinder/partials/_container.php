<?php if ($this->previewMode && !$value): ?>

    <span class="form-control" disabled="disabled">
        <?= e(trans('backend::lang.form.preview_no_record_message')) ?>
    </span>

<?php else: ?>

    <div class="pagefinder-widget" id="<?= $this->getId('container') ?>">
        <?= $this->makePartial('pagefinder') ?>
    </div>

<?php endif ?>
