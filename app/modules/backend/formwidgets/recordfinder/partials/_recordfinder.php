<?php switch ($displayMode):

    case 'single': ?>
        <?= $this->makePartial('record_single') ?>
    <?php break ?>

    <?php case 'multi': ?>
        <?= $this->makePartial('record_multi') ?>
    <?php break ?>

<?php endswitch ?>
