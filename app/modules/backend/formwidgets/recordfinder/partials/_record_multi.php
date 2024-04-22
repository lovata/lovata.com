<?php
    $previewMode = false;
    if ($this->previewMode || $field->readOnly) {
        $previewMode = true;
    }
?>
<div
    id="<?= $this->getId() ?>"
    class="field-recordfinder is-multi loading-indicator-container size-input-text <?= $value ? 'is-populated' : '' ?> <?= $previewMode ? 'is-preview' : '' ?>"
    data-control="recordfinder"
    data-refresh-handler="<?= $this->getEventHandler('onRefresh') ?>"
    data-data-locker="#<?= $field->getId() ?>">

    <div class="recordfinder-control-container">
        <?php if (!$previewMode): ?>
            <div class="recordfinder-control-toolbar">
                <button
                    class="backend-toolbar-button control-button toolbar-find-button"
                    data-control="popup"
                    data-size="<?= $popupSize ?? 'huge' ?>"
                    data-handler="<?= $this->getEventHandler('onFindRecord') ?>"
                    data-request-data="recordfinder_flag: 1"
                    type="button">
                    <i class="icon-common-file-star"></i>
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
                        <i class="icon-common-file-remove"></i>
                        <?= __("Clear") ?>
                    </button>
                <?php endif ?>
            </div>
        <?php endif ?>

        <div class="recordfinder-record-container">
            <?php if ($value): ?>
                <div class="record-item">
                    <div class="record-data-container">
                        <div class="record-data-container-inner">
                            <div class="icon-container">
                                <i class="icon-chain"></i>
                            </div>
                            <div class="info">
                                <h4 class="recordname"><?= e($nameValue) ?: 'Undefined' ?></h4>
                                <?php if ($descriptionValue): ?>
                                    <p class="description"><?= e($descriptionValue) ?></p>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>

    <input
        type="hidden"
        name="<?= $field->getName() ?>"
        id="<?= $field->getId() ?>"
        value="<?= e($value) ?>"
        />
</div>
