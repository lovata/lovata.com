<?= Form::open(['id' => 'themeForm']) ?>
    <div class="modal-header">
        <h4 class="modal-title"><?= e(trans('system::lang.install.theme_label')) ?></h4>
        <button type="button" class="btn-close" data-dismiss="popup"></button>
    </div>
    <div class="modal-body">

        <?php if ($this->fatalError): ?>
            <p class="flash-message static error"><?= e($fatalError) ?></p>
        <?php endif ?>

        <div class="form-group">
            <label class="form-label" for="themeCode"><?= e(trans('system::lang.theme.name.label')) ?></label>
            <input
                name="code"
                type="text"
                class="form-control"
                id="themeCode"
                value="<?= e(post('code')) ?>" />
            <p class="form-text"><?= e(trans('system::lang.theme.name.help')) ?></p>
        </div>

    </div>

    <div class="modal-footer">
        <button
            type="submit"
            class="btn btn-primary"
            data-dismiss="popup"
            data-control="popup"
            data-handler="<?= $this->getEventHandler('onInstallTheme') ?>">
            <?= e(trans('system::lang.install.plugin_label')) ?>
        </button>
        <button
            type="button"
            class="btn btn-default"
            data-dismiss="popup">
            <?= e(trans('backend::lang.form.cancel')) ?>
        </button>
    </div>
    <script>
        setTimeout(
            function(){ $('#themeCode').select() },
            310
        )
    </script>
<?= Form::close() ?>
