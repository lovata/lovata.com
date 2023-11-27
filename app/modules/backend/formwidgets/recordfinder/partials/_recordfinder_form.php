<div id="<?= $this->getId('popup') ?>" class="recordfinder-popup">
    <?= Form::open() ?>
        <div class="modal-header">
            <h4 class="modal-title"><?= e(__($title)) ?></h4>
            <button type="button" class="btn-close" data-dismiss="popup"></button>
        </div>

        <div class="recordfinder-list list-flush" data-request-data="recordfinder_flag: 1">
            <?= $searchWidget->render() ?>
            <?= $listWidget->render() ?>
        </div>

        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-default"
                data-dismiss="popup">
                <?= e(trans('backend::lang.recordfinder.cancel')) ?>
            </button>
        </div>
    <?= Form::close() ?>
</div>

<script>
    setTimeout(
        function(){ $('#<?= $this->getId('popup') ?> input.form-control:first').focus() },
        310
    )
</script>
