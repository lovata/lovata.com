<div class="fileupload-config-form">
    <?= Form::open() ?>
        <input type="hidden" name="file_id" value="<?= $file->id ?>" />
        <input type="hidden" name="fileupload_flag" value="1" />

        <?php if (starts_with($displayMode, 'image')): ?>
            <div class="file-upload-modal-image-header">
                <button type="button" class="btn-close" data-dismiss="popup"></button>
                <img
                    src="<?= $file->thumbUrl ?>"
                    class="img-fluid center-block"
                    alt=""
                    title="<?= __("Attachment") ?>: <?= e($file->file_name) ?>"
                    style="<?= $cssDimensions ?>" />
            </div>
        <?php else: ?>
            <div class="modal-header">
                <h4 class="modal-title"><?= __("Attachment") ?>: <?= $file->file_name ?></h4>
                <button type="button" class="btn-close" data-dismiss="popup"></button>
            </div>
        <?php endif ?>
        <div class="modal-body">
            <p><?= __("Add a title and description for this attachment.") ?></p>
            <?= $configFormWidget->render() ?>
        </div>
        <div class="modal-footer">
            <button
                type="submit"
                class="btn btn-primary"
                data-request="<?= $this->getEventHandler('onSaveAttachmentConfig') ?>"
                data-popup-load-indicator>
                <?= e(trans('backend::lang.form.save')) ?>
            </button>
            <button
                type="button"
                class="btn btn-default"
                data-dismiss="popup">
                <?= e(trans('backend::lang.form.cancel')) ?>
            </button>
            <a href="<?= $file->pathUrl ?>" class="pull-right btn btn-link fileupload-url-button" target="_blank">
                <i class="oc-icon-link"></i><?= __("Attachment URL") ?>
            </a>
        </div>
    <?= Form::close() ?>
</div>
