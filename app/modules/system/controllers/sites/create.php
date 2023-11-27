<?php Block::put('breadcrumb') ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Backend::url('system/sites') ?>"><?= __("Sites") ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
    </ol>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <?= Form::open(['class' => 'layout mw-950']) ?>

        <div class="layout-row">
            <?= $this->formRender() ?>
        </div>

        <div class="form-buttons pt-4">
            <div class="loading-indicator-container">
                <button
                    type="submit"
                    data-request="onSave"
                    data-hotkey="ctrl+s, cmd+s"
                    data-load-indicator="<?= __("Creating") ?>"
                    class="btn btn-primary">
                    <?= __("Create") ?>
                </button>
                <button
                    type="button"
                    data-request="onSave"
                    data-request-data="close:1"
                    data-hotkey="ctrl+enter, cmd+enter"
                    data-load-indicator="<?= __("Creating") ?>"
                    class="btn btn-default">
                    <?= __("Create and Close") ?>
                </button>
                <span class="btn-text">
                    <?= __("or") ?> <a href="<?= Backend::url('system/sites') ?>"><?= __("Cancel") ?></a>
                </span>
            </div>
        </div>

    <?= Form::close() ?>

<?php else: ?>

    <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>
    <p><a href="<?= Backend::url('system/sites') ?>" class="btn btn-default"><?= __("Return") ?></a></p>

<?php endif ?>
