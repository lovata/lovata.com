<?php if (!$this->fatalError): ?>

    <?= $this->listRender() ?>

<?php else: ?>

    <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>

<?php endif ?>
