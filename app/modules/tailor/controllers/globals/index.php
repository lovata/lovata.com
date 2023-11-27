<?php if (!$this->fatalError): ?>
    <?php Block::put('breadcrumb') ?>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= Backend::url('tailor/globals/'.$activeSource->handleSlug) ?>"><?= $activeSource->name ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
        </ol>
    <?php Block::endPut() ?>

    <?= Form::open(['class' => 'layout']) ?>
        <div class="layout-row">
            <div class="mw-<?= $formSize ?>">
                <?= $this->formRender() ?>
            </div>
        </div>

        <div class="form-buttons">
            <div class="loading-indicator-container">
                <button
                    type="submit"
                    data-request="onSave"
                    data-request-data="redirect:0"
                    data-hotkey="ctrl+s, cmd+s"
                    data-load-indicator="<?= e(trans('backend::lang.form.saving_name', ['name'=>$entityName])) ?>"
                    class="btn btn-primary">
                    <?= __('Save Changes') ?>
                </button>
                <span class="btn-text">
                    <?= e(trans('backend::lang.form.or')) ?>
                    <a href="<?= Backend::url('tailor/globals/'.$activeSource->handleSlug) ?>">
                        <?= e(trans('backend::lang.form.cancel')) ?>
                    </a>
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
    <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>
    <p><a href="<?= Backend::url('tailor/globals') ?>" class="btn btn-default"><?= __("Return to Globals") ?></a></p>
<?php endif ?>
