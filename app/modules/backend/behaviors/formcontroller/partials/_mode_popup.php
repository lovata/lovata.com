<?= Form::open(['id' => $this->formGetId('managePopup')]) ?>
    <input type="hidden" name="form_popup_flag" value="1" />
    <input type="hidden" name="form_record_id" value="<?= $recordId ?? '' ?>" />

    <div class="modal-header">
        <h4 class="modal-title"><?= e($popupTitle ?? '') ?></h4>
        <button type="button" class="btn-close" data-dismiss="popup"></button>
    </div>

    <div class="modal-body" data-popup-size="<?= $popupSize ?? 950 ?>">
        <?= $this->formRender() ?>
    </div>

    <div class="modal-footer">
        <div class="form-buttons">
            <?= $this->formRender(['section' => 'buttons']) ?>
        </div>
    </div>

    <script>
        setTimeout(function() {
            $('#<?= $this->formGetId('managePopup') ?> input.form-control:first').focus();
        }, 310);
    </script>

<?= Form::close() ?>

<script>
    oc.popup.bindToPopups('#<?= $this->formGetId('managePopup') ?>', {
        form_popup_flag: 1,
        form_record_id: '<?= $recordId ?? '' ?>'
    });
</script>
