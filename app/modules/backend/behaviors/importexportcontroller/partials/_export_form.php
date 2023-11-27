<div id="exportFormPopup">
    <?php if (!$this->fatalError): ?>

        <?= Form::open(['id' => 'exportForm']) ?>
            <div class="modal-header">
                <h4 class="modal-title"><?= e(__('Export progress')) ?></h4>
            </div>

            <div id="exportContainer">
                <div class="modal-body">

                    <div class="loading-indicator-container">
                        <p>&nbsp;</p>
                        <div class="loading-indicator is-transparent">
                            <div><?= e(__('Processing')) ?></div>
                            <span></span>
                        </div>
                    </div>
                    <p>&nbsp;</p>

                </div>
            </div>
        <?= Form::close() ?>

        <script>
            $('#exportFormPopup').on('popupComplete', function() {
                $.oc.exportBehavior.processExport();
            });
        </script>

    <?php else: ?>

        <div class="modal-header">
            <h4 class="modal-title"><?= e(__('Export error')) ?></h4>
            <button type="button" class="btn-close" data-dismiss="popup"></button>
        </div>
        <div class="modal-body">
            <p class="flash-message static error"><?= e($this->fatalError) ?></p>
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
</div>
