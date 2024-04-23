<?php if (!$this->fatalError): ?>

    <?= Form::open(['class'=>'layout']) ?>
        <div class="layout-row">
            <?= $this->formRender() ?>
        </div>

        <div class="form-buttons">
            <div data-control="loader-container">
                <?= Ui::ajaxButton("Save Changes", 'onSave')
                    ->primary()
                    ->ajaxData(['redirect' => false])
                    ->hotkey('ctrl+s', 'cmd+s')
                    ->loadingMessage(__("Saving...")) ?>

                <span class="btn-text">
                    <span class="button-separator"><?= __("or") ?></span>
                    <?= Ui::button("Cancel", 'system/settings')
                        ->textLink() ?>
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
    <p><a href="<?= Backend::url('system/settings') ?>" class="btn btn-default"><?= __('Return to System Settings') ?></a></p>
<?php endif ?>
