<div>
    <?php if (!$this->formGetModel()->exists): ?>
        <?= Ui::ajaxButton(__("Create"), 'onPopupSave')
            ->primary()
            ->ajaxData(['redirect' => false])
            ->hotkey('ctrl+s', 'cmd+s')
            ->loadingPopup() ?>
    <?php else: ?>
        <?= Ui::ajaxButton(__("Save"), 'onPopupSave')
            ->ajaxData(['redirect' => false])
            ->primary()
            ->hotkey('ctrl+s', 'cmd+s')
            ->loadingPopup() ?>

        <?php if ($this->formCheckPermission('modelDelete')): ?>
            <?= Ui::ajaxButton(__("Save and Close"), 'onPopupDelete')
                ->formDeleteButton()
                ->confirmMessage(__("Delete this record?"))
                ->loadingPopup() ?>
        <?php endif ?>
    <?php endif ?>

    <span class="btn-text">
        <span class="button-separator"><?= __("or") ?></span>
        <?= Ui::ajaxButton(__("Cancel"), 'onPopupCancel')
            ->textLink()
            ->redirectBack()
            ->ajaxData(['close' => true])
            ->dismissPopup() ?>
    </span>
</div>
