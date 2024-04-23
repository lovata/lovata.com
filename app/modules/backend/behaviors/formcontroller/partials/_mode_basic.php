<?php
    $isHorizontal = $this->formGetWidget()->horizontalMode;
?>
<?= Form::open(['class' => 'd-flex flex-column h-100 design-basic']) ?>

    <div class="flex-grow-1">
        <?= $this->formRender($options) ?>
    </div>

    <?php if ($this->formGetContext() !== 'preview'): ?>
        <div class="form-buttons pt-3 <?= $isHorizontal ? 'is-horizontal' : '' ?>">
            <div data-control="loader-container">
                <?= $this->formRender(['section' => 'buttons']) ?>
            </div>
        </div>
    <?php endif ?>

<?= Form::close() ?>
