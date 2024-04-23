<?= Form::open(['id' => 'themeCheckForm']) ?>
    <input name="code" value="<?= e($code) ?>" type="hidden" />

    <div class="modal-header">
        <h4 class="modal-title"><?= __("Select Installation Method") ?></h4>
        <button type="button" class="btn-close" data-dismiss="popup"></button>
    </div>
    <div class="modal-body">
        <?= $formWidget->render() ?>
    </div>

    <div class="modal-footer">
        <button
            type="submit"
            class="btn btn-primary"
            data-dismiss="popup"
            data-control="popup"
            data-handler="<?= $this->getEventHandler('onInstallTheme') ?>">
            <?= __("Install Theme") ?>
        </button>
        <button
            type="button"
            class="btn btn-secondary"
            data-dismiss="popup">
            <?= __("Cancel") ?>
        </button>
    </div>
    <script>
        setTimeout(
            function(){ $('#themeCode').select() },
            310
        )
    </script>
<?= Form::close() ?>
