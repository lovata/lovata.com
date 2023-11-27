<div id="<?= $relationManageWidget->getId('managePopup') ?>">
    <?php if ($relationManageId): ?>

        <?= Form::ajax('onRelationManageUpdate', [
            'sessionKey' => $newSessionKey,
            'data-popup-load-indicator' => true,
            'data-request-success' => "$.oc.relationBehavior.changed('" . e($relationField) . "', 'updated')",
        ]) ?>

            <!-- Passable fields -->
            <input type="hidden" name="manage_id" value="<?= $relationManageId ?>" />
            <input type="hidden" name="_relation_field" value="<?= $relationField ?>" />
            <input type="hidden" name="_relation_mode" value="form" />
            <input type="hidden" name="_relation_session_key" value="<?= $relationSessionKey ?>" />

            <div class="modal-header">
                <h4 class="modal-title"><?= e($relationManageTitle) ?></h4>
                <button type="button" class="btn-close" data-dismiss="popup"></button>
            </div>

            <div class="modal-body">
                <?= $relationManageWidget->render(['preview' => $this->readOnly]) ?>
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

        <?= Form::ajax('onRelationManageCreate', [
            'sessionKey' => $newSessionKey,
            'data-popup-load-indicator' => true,
            'data-request-success' => "$.oc.relationBehavior.changed('" . e($relationField) . "', 'created')",
        ]) ?>

            <!-- Passable fields -->
            <input type="hidden" name="_relation_field" value="<?= $relationField ?>" />
            <input type="hidden" name="_relation_mode" value="form" />
            <input type="hidden" name="_relation_session_key" value="<?= $relationSessionKey ?>" />

            <div class="modal-header">
                <h4 class="modal-title"><?= e($relationManageTitle) ?></h4>
                <button type="button" class="btn-close" data-dismiss="popup"></button>
            </div>

            <div class="modal-body">
                <?= $relationManageWidget->render() ?>
            </div>

            <div class="modal-footer">
                <button
                    type="submit"
                    class="btn btn-primary">
                    <?= e(trans('backend::lang.relation.create')) ?>
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
    $.oc.relationBehavior.bindToPopups('#<?= $relationManageWidget->getId("managePopup") ?>', {
        _relation_field: '<?= $relationField ?>',
        _relation_mode: 'form'
    });
</script>
