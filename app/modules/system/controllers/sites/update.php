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
                    data-request-data="redirect:0"
                    data-hotkey="ctrl+s, cmd+s"
                    data-load-indicator="<?= __("Saving") ?>"
                    class="btn btn-primary">
                    <?= __("Save") ?>
                </button>
                <button
                    type="button"
                    data-request="onSave"
                    data-request-data="close:1"
                    data-hotkey="ctrl+enter, cmd+enter"
                    data-load-indicator="<?= __("Saving") ?>"
                    class="btn btn-default">
                    <?= __("Save and Close") ?>
                </button>
                <?php if (!$formModel->is_primary): ?>
                    <button
                        type="button"
                        class="oc-icon-trash-o btn-icon danger pull-right"
                        data-request="onDelete"
                        data-load-indicator="<?= __("Deleting") ?>"
                        data-request-confirm="<?= __("Are you sure?") ?>">
                    </button>
                <?php endif ?>
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
