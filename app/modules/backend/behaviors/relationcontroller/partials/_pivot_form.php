<div id="<?= $relationPivotWidget->getId('pivotPopup') ?>">
    <?php if ($relationManageId): ?>

        <?= Form::ajax('onRelationManagePivotUpdate', [
            'data-popup-load-indicator' => true,
            'data-request-success' => "oc.relationBehavior.changed('" . e($relationField) . "', 'updated')",
        ]) ?>

            <!-- Passable fields -->
            <input type="hidden" name="_relation_field" value="<?= $relationField ?>" />
            <input type="hidden" name="_relation_extra_config" value="<?= e(json_encode($relationExtraConfig)) ?>" />

            <div class="modal-header" data-popup-size="<?= $relationPopupSize ?? 950 ?>">
                <h4 class="modal-title"><?= e($relationPivotTitle) ?></h4>
                <button type="button" class="btn-close" data-dismiss="popup"></button>
            </div>
            <div class="modal-body">
                <?= $relationPivotWidget->render(['preview' => $relationReadOnly]) ?>
            </div>
            <div class="modal-footer">
                <?php if ($relationReadOnly): ?>
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="popup">
                        <?= e($this->relationGetMessage('buttonCloseForm')) ?>
                    </button>
                <?php else: ?>
                    <button
                        type="submit"
                        class="btn btn-primary">
                        <?= __("Update") ?>
                    </button>
                    <span class="btn-text">
                        <span class="button-separator"><?= __("or") ?></span>
                        <a
                            href="javascript:;"
                            class="btn btn-link p-0"
                            data-dismiss="popup">
                            <?= e($this->relationGetMessage('buttonCancelForm')) ?>
                        </a>
                    </span>
                <?php endif ?>
            </div>

        <?= Form::close() ?>

    <?php else: ?>

        <?= Form::ajax('onRelationManagePivotCreate', [
            'data-popup-load-indicator' => true,
            'data-request-success' => "oc.relationBehavior.changed('" . e($relationField) . "', 'created')",
        ]) ?>

            <!-- Passable fields -->
            <input type="hidden" name="_relation_field" value="<?= $relationField ?>" />
            <input type="hidden" name="_relation_extra_config" value="<?= e(json_encode($relationExtraConfig)) ?>" />
            <?php foreach ((array) $foreignId as $fid): ?>
                <input type="hidden" name="foreign_id[]" value="<?= $fid ?>" />
            <?php endforeach ?>

            <div class="modal-header" data-popup-size="<?= $relationPopupSize ?? 950 ?>">
                <h4 class="modal-title"><?= e($relationPivotTitle) ?></h4>
                <button type="button" class="btn-close" data-dismiss="popup"></button>
            </div>
            <div class="modal-body">
                <?= $relationPivotWidget->render() ?>
            </div>
            <div class="modal-footer">
                <button
                    type="submit"
                    class="btn btn-primary">
                    <?= e($this->relationGetMessage('buttonAddForm')) ?>
                </button>
                <span class="btn-text">
                    <span class="button-separator"><?= __("or") ?></span>
                    <a
                        href="javascript:;"
                        class="btn btn-link p-0"
                        data-dismiss="popup">
                        <?= e($this->relationGetMessage('buttonCancelForm')) ?>
                    </a>
                </span>
            </div>

        <?= Form::close() ?>

    <?php endif ?>

</div>

<script>
    oc.popup.bindToPopups('#<?= $relationPivotWidget->getId("pivotPopup") ?>', {
        _relation_field: '<?= $relationField ?>'
    });
</script>
