<div id="<?= $relationManageWidget->getId('pivotPopup') ?>">
    <?php if ($relationManageId): ?>

        <?= Form::ajax('onRelationManagePivotUpdate', [
            'data-popup-load-indicator' => true,
            'data-request-success' => "$.oc.relationBehavior.changed('" . e($relationField) . "', 'updated')",
        ]) ?>

            <!-- Passable fields -->
            <input type="hidden" name="manage_id" value="<?= $relationManageId ?>" />
            <input type="hidden" name="_relation_field" value="<?= $relationField ?>" />

            <div class="modal-header">
                <h4 class="modal-title"><?= e($relationPivotTitle) ?></h4>
                <button type="button" class="btn-close" data-dismiss="popup"></button>
            </div>
            <div class="modal-body">
                <?= $relationPivotWidget->render(['preview' => $this->readOnly]) ?>
            </div>
            <div class="modal-footer">
                <?php if ($this->readOnly): ?>
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="popup">
                        <?= e(trans('backend::lang.relation.close')) ?>
                    </button>
                <?php else: ?>
                    <button
                        type="submit"
                        class="btn btn-primary">
                        <?= e(trans('backend::lang.relation.update')) ?>
                    </button>
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="popup">
                        <?= e(trans('backend::lang.relation.cancel')) ?>
                    </button>
                <?php endif ?>
            </div>

        <?= Form::close() ?>

    <?php else: ?>

        <?= Form::ajax('onRelationManagePivotCreate', [
            'data-popup-load-indicator' => true,
            'data-request-success' => "$.oc.relationBehavior.changed('" . e($relationField) . "', 'created')",
        ]) ?>

            <!-- Passable fields -->
            <input type="hidden" name="_relation_field" value="<?= $relationField ?>" />
            <?php foreach ((array) $foreignId as $fid): ?>
                <input type="hidden" name="foreign_id[]" value="<?= $fid ?>" />
            <?php endforeach ?>

            <div class="modal-header">
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
                    <?= e(trans('backend::lang.relation.add')) ?>
                </button>
                <button
                    type="button"
                    class="btn btn-default"
                    data-dismiss="popup">
                    <?= e(trans('backend::lang.relation.cancel')) ?>
                </button>
            </div>

        <?= Form::close() ?>

    <?php endif ?>

</div>

<script>
    $.oc.relationBehavior.bindToPopups('#<?= $relationManageWidget->getId("pivotPopup") ?>', {
        _relation_field: '<?= $relationField ?>',
        _relation_mode: 'pivot'
    });
</script>
