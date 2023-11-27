<?php Block::put('breadcrumb') ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Backend::url('cms/themes') ?>"><?= __('Themes') ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
    </ol>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <?php if ($hasCustomData): ?>
        <?= Form::open(['class'=>'layout']) ?>

            <div class="layout-row">
                <?= $this->formRender() ?>
            </div>

            <div class="form-buttons">
                <div class="loading-indicator-container">
                    <button
                        type="submit"
                        data-request="onSave"
                        data-request-data="redirect:0"
                        data-hotkey="ctrl+s, cmd+s"
                        data-load-indicator="<?= __('Saving Theme...') ?>"
                        class="btn btn-primary">
                        <?= e(trans('backend::lang.form.save')) ?>
                    </button>
                    <button
                        type="button"
                        data-request="onSave"
                        data-request-data="close:1"
                        data-hotkey="ctrl+enter, cmd+enter"
                        data-load-indicator="<?= __('Saving Theme...') ?>"
                        class="btn btn-default">
                        <?= e(trans('backend::lang.form.save_and_close')) ?>
                    </button>

                    <span class="btn-text">
                        <?= e(trans('backend::lang.form.or')) ?> <a href="<?= Backend::url('cms/themes') ?>"><?= e(trans('backend::lang.form.cancel')) ?></a>
                    </span>

                    <button
                        type="button"
                        class="btn btn-danger pull-right"
                        data-request="onResetDefault"
                        data-load-indicator="<?= e(trans('backend::lang.form.resetting')) ?>"
                        data-request-confirm="<?= e(trans('backend::lang.form.action_confirm')) ?>">
                        <?= e(trans('backend::lang.form.reset_default')) ?>
                    </button>
                </div>
            </div>

        <?= Form::close() ?>

    <?php else: ?>
        <div class="callout callout-info no-title">
            <div class="content">
                <p>There are no theme options available to customize.</p>
            </div>
        </div>
    <?php endif ?>

<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= Backend::url('cms/themes') ?>" class="btn btn-default"><?= __('Return to Themes List') ?></a></p>

<?php endif ?>
