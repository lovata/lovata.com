<?= Form::open(['id' => 'themeForm']) ?>
    <div class="modal-header">
        <h4 class="modal-title"><?= __("Install Theme") ?></h4>
        <button type="button" class="btn-close" data-dismiss="popup"></button>
    </div>
    <div class="modal-body">

        <?php if ($this->fatalError): ?>
            <p class="flash-message static error"><?= e($fatalError) ?></p>
        <?php endif ?>

        <div class="form-group">
            <label class="form-label" for="themeCode"><?= __("Theme Name") ?></label>
            <input
                name="code"
                type="text"
                class="form-control"
                id="themeCode"
                value="<?= e(post('code')) ?>" />
            <p class="form-text"><?= __("Name the theme by its unique code. For example, RainLab.Vanilla") ?></p>
        </div>

    </div>

    <div class="modal-footer">
        <button
            type="submit"
            class="btn btn-primary"
            data-dismiss="popup"
            data-control="popup"
            data-handler="<?= $this->getEventHandler('onInstallThemeCheck') ?>">
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
