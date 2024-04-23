<div>
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
