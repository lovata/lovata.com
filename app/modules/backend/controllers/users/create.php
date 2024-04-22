<?php Block::put('breadcrumb') ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Backend::url('backend/users') ?>"><?= __("Administrators") ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
    </ol>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <?php Block::put('form-contents') ?>
        <div class="layout">

            <div class="layout-row">
                <?= $this->formRenderOutsideFields() ?>
                <?= $this->formRenderPrimaryTabs() ?>
            </div>

            <div class="form-buttons">
                <div class="loading-indicator-container">
                    <button
                        type="submit"
                        data-request="onSave"
                        data-hotkey="ctrl+s, cmd+s"
                        data-load-indicator="<?= e(trans('backend::lang.form.creating')) ?>"
                        class="btn btn-primary">
                        <?= e(trans('backend::lang.form.create')) ?>
                    </button>
                    <button
                        type="button"
                        data-request="onSave"
                        data-request-data="close:1"
                        data-hotkey="ctrl+enter, cmd+enter"
                        data-load-indicator="<?= e(trans('backend::lang.form.creating')) ?>"
                        class="btn btn-default">
                        <?= e(trans('backend::lang.form.create_and_close')) ?>
                    </button>
                    <span class="btn-text">
                        <?= e(trans('backend::lang.form.or')) ?> <a href="<?= Backend::url('backend/users') ?>"><?= e(trans('backend::lang.form.cancel')) ?></a>
                    </span>
                </div>
            </div>

        </div>
    <?php Block::endPut() ?>

    <?php Block::put('form-sidebar') ?>
        <div class="hide-tabs"><?= $this->formRenderSecondaryTabs() ?></div>
    <?php Block::endPut() ?>

    <?php Block::put('body') ?>
        <?= Form::open(['class'=>'position-relative h-100']) ?>
            <?= $this->makeLayout('form-with-sidebar') ?>
        <?= Form::close() ?>
    <?php Block::endPut() ?>

<?php else: ?>
    <nav class="control-breadcrumb">
        <?= Block::placeholder('breadcrumb') ?>
    </nav>
    <div class="padded-container">
        <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>
        <p><a href="<?= Backend::url('backend/users') ?>" class="btn btn-default"><?= e(trans('backend::lang.user.return')) ?></a></p>
    </div>
<?php endif ?>
