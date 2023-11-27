<?php
    $previewMode = false;
    if ($this->previewMode || $field->readOnly) {
        $previewMode = true;
    }
?>
<div
    id="<?= $this->getId() ?>"
    class="field-recordfinder is-single loading-indicator-container size-input-text <?= $value ? 'is-populated' : '' ?> <?= $previewMode ? 'is-preview' : '' ?>"
    data-control="recordfinder"
    data-refresh-handler="<?= $this->getEventHandler('onRefresh') ?>"
    data-data-locker="#<?= $field->getId() ?>">

    <div class="recordfinder-control-container d-flex">
        <?php if ($value): ?>
            <div class="recordfinder-record-container me-auto">
                <div class="record-item">
                    <div class="record-data-container">
                        <div class="record-data-container-inner">
                            <div class="icon-container">
                                <i class="octo-icon-chain"></i>
                            </div>
                            <div class="info">
                                <span class="recordname"><?= e($nameValue) ?: 'Undefined' ?></span>
                                <?php if ($descriptionValue): ?>
                                    <span class="description"> - <?= e($descriptionValue) ?></span>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>
        <?php if (!$previewMode): ?>
            <div class="recordfinder-control-toolbar">
                <button
                    class="backend-toolbar-button control-button toolbar-find-button"
                    data-control="popup"
                    data-size="huge"
                    data-handler="<?= $this->getEventHandler('onFindRecord') ?>"
                    data-request-data="recordfinder_flag: 1"
                    type="button">
                    <i class="octo-icon-common-file-star"></i>
                    <?php if ($value): ?>
                        <?= __("Replace") ?>
                    <?php else: ?>
                        <?= __("Select") ?>
                    <?php endif ?>
                </button>
                <?php if ($value): ?>
                    <button
                        type="button"
                        class="backend-toolbar-button control-button find-remove-button"
                        data-request="<?= $this->getEventHandler('onClearRecord') ?>"
                        data-request-confirm="<?= e(trans('backend::lang.form.action_confirm')) ?>"
                        data-request-success="$('#<?= $field->getId() ?>').val('').trigger('change')"
                        aria-label="Remove">
                        <i class="octo-icon-common-file-remove"></i>
                        <?= __("Clear") ?>
                    </button>
                <?php endif ?>
            </div>
        <?php endif ?>

    </div>

    <input
        type="hidden"
        name="<?= $field->getName() ?>"
        id="<?= $field->getId() ?>"
        value="<?= e($value) ?>"
        />
</div>
