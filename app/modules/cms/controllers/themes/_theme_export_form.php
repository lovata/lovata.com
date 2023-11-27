<?= Form::ajax('onExport', [
    'id' => 'themeExportForm',
    'data-popup-load-indicator' => true,
    'data-request-success' => 'closeExportThemePopup()'
]) ?>

    <input type="hidden" name="theme" value="<?= $themeDir ?>" />

    <div class="modal-header">
        <h4 class="modal-title"><?= __('Export Theme') ?></h4>
        <button type="button" class="btn-close" data-dismiss="popup"></button>
    </div>

    <?php if (!$this->fatalError): ?>

        <div class="modal-body">
            <?= $widget->render() ?>
        </div>
        <div class="modal-footer">
            <button
                type="submit"
                class="btn btn-success">
                <?= __('Export') ?>
            </button>

            <button
                type="button"
                class="btn btn-default"
                data-dismiss="popup">
                <?= e(trans('backend::lang.form.cancel')) ?>
            </button>
        </div>

    <?php else: ?>

        <div class="modal-body">
            <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>
        </div>
        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-default"
                data-dismiss="popup">
                <?= e(trans('backend::lang.form.close')) ?>
            </button>
        </div>

    <?php endif ?>

    <script>
        setTimeout(
            function(){ $('#themeExportForm input.form-control:first').focus() },
            310
        )

        function closeExportThemePopup() {
            $('#themeExportForm')
                .closest('.control-popup')
                .popup('hideLoading')
        }
    </script>

<?= Form::close() ?>
