<div>
    <?php if (!$this->formGetModel()->exists): ?>
        <?= Ui::ajaxButton(__("Create"), 'onSave')
            ->primary()
            ->hotkey('ctrl+s', 'cmd+s')
            ->loadingMessage(__("Creating :name...", ['name' => $formRecordName])) ?>

        <?= Ui::ajaxButton(__("Create & Close"), 'onSave')
            ->secondary()
            ->redirectBack()
            ->ajaxData(['close' => true])
            ->hotkey('ctrl+enter', 'cmd+enter')
            ->loadingMessage(__("Creating :name...", ['name' => $formRecordName])) ?>
    <?php else: ?>
        <?= Ui::ajaxButton(__("Save"), 'onSave')
            ->primary()
            ->ajaxData(['redirect' => false])
            ->hotkey('ctrl+s', 'cmd+s')
            ->loadingMessage(__("Saving :name...", ['name' => $formRecordName])) ?>

        <?= Ui::ajaxButton(__("Save & Close"), 'onSave')
            ->secondary()
            ->redirectBack()
            ->ajaxData(['close' => true])
            ->hotkey('ctrl+enter', 'cmd+enter')
            ->loadingMessage(__("Saving :name...", ['name' => $formRecordName])) ?>

        <?php if (!$formModel->is_primary): ?>
            <?= Ui::ajaxButton(__("Delete"), 'onDelete')
                ->formDeleteButton()
                ->redirectBack()
                ->confirmMessage(__("Delete this record?"))
                ->loadingMessage(__("Deleting :name...", ['name' => $formRecordName])) ?>
        <?php endif ?>
    <?php endif ?>

    <span class="btn-text">
        <span class="button-separator"><?= __("or") ?></span>
        <?= Ui::ajaxButton(__("Cancel"), 'onCancel')
            ->textLink()
            ->redirectBack()
            ->ajaxData(['close' => true])
            ->loadingMessage(__("Loading...")) ?>
    </span>
</div>
