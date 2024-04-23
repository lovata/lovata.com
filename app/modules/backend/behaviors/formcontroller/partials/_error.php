<div class="form-fatal-error">
    <p class="flash-message static error">
        <?= e($fatalError) ?>
    </p>
    <p>
        <?= Ui::button("Return to Previous Page", $this->formGetConfig()->defaultRedirect ?? '')
            ->icon('icon-arrow-left')
            ->secondary()
            ->redirectBack() ?>
    </p>
</div>
