<?= Form::open(['id' => 'projectForm']) ?>
    <div class="modal-header">
        <h4 class="modal-title"><?= __('Attach to Project') ?></h4>
        <button type="button" class="btn-close" data-dismiss="popup"></button>
    </div>
    <div class="modal-body">

        <?php if ($this->fatalError): ?>
            <p class="flash-message static error"><?= e($fatalError) ?></p>
        <?php endif ?>

        <div class="form-group">
            <span class="help-block pull-right">
                <a target="_blank" href="http://octobercms.com/help/site/projects#project-id"><?= __('How to find your License Key') ?></a>
            </span>
            <label class="form-label" for="projectId"><?= __('License Key') ?></label>
            <input
                name="project_id"
                type="text"
                class="form-control"
                id="projectId"
                value="<?= e(post('project_id')) ?>"
                autocomplete="off" />
        </div>

    </div>

    <div class="modal-footer">
        <button
            type="submit"
            class="btn btn-primary"
            data-request="onAttachProject"
            data-popup-load-indicator>
            <?= __('Attach to Project') ?>
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
            function(){ $('#projectId').select() },
            310
        )
    </script>
<?= Form::close() ?>
