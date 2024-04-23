<?= Form::open(['id' => 'pluginForm']) ?>
    <div class="modal-header">
        <h4 class="modal-title"><?= __("Install Plugin") ?></h4>
        <button type="button" class="btn-close" data-dismiss="popup"></button>
    </div>
    <div class="modal-body">

        <?php if ($this->fatalError): ?>
            <p class="flash-message static error"><?= e($fatalError) ?></p>
        <?php endif ?>

        <div class="form-group">
            <label class="form-label" for="pluginCode"><?= __("Plugin Name") ?></label>
            <input
                name="code"
                type="text"
                class="form-control"
                id="pluginCode"
                value="<?= e(post('code')) ?>" />
            <p class="form-text"><?= __("Name the plugin by its unique code. For example, RainLab.Blog") ?></p>
        </div>

    </div>

    <div class="modal-footer">
        <button
            type="submit"
            class="btn btn-primary"
            data-dismiss="popup"
            data-control="popup"
            data-handler="<?= $this->getEventHandler('onInstallPlugin') ?>">
            <?= __("Install Plugin") ?>
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
            function(){ $('#pluginCode').select() },
            310
        )
    </script>
<?= Form::close() ?>
