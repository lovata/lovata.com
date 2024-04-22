<?php if (!$this->fatalError): ?>
    <?php Block::put('breadcrumb') ?>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= Backend::url('tailor/globals/'.$activeSource->handleSlug) ?>"><?= $activeSource->name ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
        </ol>
    <?php Block::endPut() ?>

    <?= Form::open(['class' => 'layout design-settings']) ?>
        <div class="layout-row">
            <?= $this->formRender() ?>
        </div>

        <div class="form-buttons">
            <div data-control="loader-container">
                <?= Ui::ajaxButton("Save Changes", 'onSave')
                    ->primary()
                    ->ajaxData(['redirect' => false])
                    ->hotkey('ctrl+s', 'cmd+s')
                    ->loadingMessage(__("Saving :name...", ['name' => $entityName])) ?>
                <span class="btn-text">
                    <span class="button-separator"><?= __("or") ?></span>
                    <?= Ui::ajaxButton("Cancel", 'onCancel')
                        ->textLink()
                        ->loadingMessage(__("Loading...")) ?>
                </span>
                <span class="pull-right btn-text">
                    <?= Ui::ajaxButton("Reset to Default", 'onResetDefault')
                        ->textLink()
                        ->ajaxData(['redirect' => false])
                        ->confirmMessage(__("Are you sure?"))
                        ->loadingMessage(__("Resetting...")) ?>
                </span>
            </div>
        </div>
    <?= Form::close() ?>

<?php else: ?>
    <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>
    <p><a href="<?= Backend::url('tailor/globals') ?>" class="btn btn-default"><?= __("Return to Globals") ?></a></p>
<?php endif ?>
