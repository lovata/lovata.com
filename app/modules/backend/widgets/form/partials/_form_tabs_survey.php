
<?php
    $contentCss = '';
    $paneCss = '';

    if ($tabs->stretch) {
        $contentCss = 'align-items-stretch';
        $paneCss = '';
    }
?>
<div class="row <?= $contentCss ?>">
    <?php $index = 0; foreach ($tabs as $name => $fields): $index++ ?>
        <?php
            $isAdaptive = $tabs->isAdaptive($name);
        ?>
        <div class="col-12 <?= e($tabs->getPaneCssClass($index, $name)) ?> <?= $paneCss ?>">
            <h4 class="my-3 fw-normal"><?= e(trans($name)) ?></h4>
            <?= $this->makePartial('form_fields', ['fields' => $fields]) ?>
        </div>
    <?php endforeach ?>
</div>
