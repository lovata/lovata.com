<div id="<?= $this->getId('popup') ?>" class="recordfinder-popup">
    <?= Form::open([
        'data-request-parent-form' => "#{$this->getId()}"
    ]) ?>
        <input type="hidden" name="recordfinder_flag" value="1" />

        <div class="modal-header">
            <h4 class="modal-title"><?= e(__($title)) ?></h4>
            <button type="button" class="btn-close" data-dismiss="popup"></button>
        </div>

        <div class="recordfinder-list list-flush">
            <?php if ($searchWidget): ?>
                <?= $searchWidget->render() ?>
            <?php endif ?>
            <?php if ($filterWidget): ?>
                <?= $filterWidget->render() ?>
            <?php endif ?>
            <?= $listWidget->render() ?>
        </div>

        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-secondary me-auto"
                data-dismiss="popup">
                <?= __("Close") ?>
            </button>
            <?php if ($listWidget->showSetup): ?>
                <button
                    class="btn btn-circle btn-secondary ms-auto"
                    title="<?= __("List Setup") ?>"
                    data-handler="<?= $listWidget->getEventHandler('onLoadSetup') ?>"
                    data-control="popup"
                    type="button">
                        <i class="icon-text-format-ul"></i>
                </button>
            <?php endif ?>
        </div>
    <?= Form::close() ?>
</div>

<script>
    setTimeout(
        function(){ $('#<?= $this->getId('popup') ?> input.form-control:first').focus() },
        310
    )
</script>
