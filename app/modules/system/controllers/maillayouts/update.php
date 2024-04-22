<?php Block::put('breadcrumb') ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Backend::url('system/mailtemplates/index/layouts') ?>"><?= __("Mail Layouts") ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
    </ol>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <?= Form::open(['class'=>'layout']) ?>

        <div class="layout-row min-size">
            <div class="scoreboard">
                <div data-control="toolbar">
                    <div class="scoreboard-item title-value">
                        <h4><?= __("Layout") ?></h4>
                        <p><?= e($formModel->code) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="layout-row">
            <?= $this->formRender() ?>
        </div>

        <div class="form-buttons pt-4">
            <div class="loading-indicator-container">
                <button
                    type="submit"
                    data-request="onSave"
                    data-request-data="redirect:0"
                    data-hotkey="ctrl+s, cmd+s"
                    data-load-indicator="<?= e(trans('system::lang.mail_templates.saving_layout')) ?>"
                    class="btn btn-primary">
                    <?= e(trans('backend::lang.form.save')) ?>
                </button>
                <button
                    type="button"
                    data-request="onSave"
                    data-request-data="close:1"
                    data-hotkey="ctrl+enter, cmd+enter"
                    data-load-indicator="<?= e(trans('system::lang.mail_templates.saving_layout')) ?>"
                    class="btn btn-default">
                    <?= e(trans('backend::lang.form.save_and_close')) ?>
                </button>
                <?php if ($formModel->is_locked): ?>
                    <button
                        type="button"
                        class="btn btn-danger pull-right"
                        data-request="onResetDefault"
                        data-load-indicator="<?= e(trans('backend::lang.form.resetting')) ?>"
                        data-request-confirm="<?= e(trans('backend::lang.form.action_confirm')) ?>">
                        <?= __("Reset to Default") ?>
                    </button>
                <?php else: ?>
                    <button
                        type="button"
                        class="oc-icon-trash btn-icon danger pull-right"
                        data-request="onDelete"
                        data-load-indicator="<?= e(trans('system::lang.mail_templates.deleting_layout')) ?>"
                        data-request-confirm="<?= e(trans('system::lang.mail_templates.delete_layout_confirm')) ?>">
                    </button>
                <?php endif ?>
                <span class="btn-text">
                    <?= e(trans('backend::lang.form.or')) ?> <a href="<?= Backend::url('system/mailtemplates/index/layouts') ?>"><?= e(trans('backend::lang.form.cancel')) ?></a>
                </span>
            </div>
        </div>

    <?= Form::close() ?>

<?php else: ?>

    <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>
    <p><a href="<?= Backend::url('system/mailtemplates/index/layouts') ?>" class="btn btn-default"><?= e(trans('system::lang.mail_templates.return')) ?></a></p>

<?php endif ?>
