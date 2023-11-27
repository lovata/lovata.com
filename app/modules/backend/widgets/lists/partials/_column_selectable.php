<?php
if (is_array($value)) {
    $selectedValues = array_map(function ($value) use ($fieldOptions) {
        return $fieldOptions[$value];
    }, $value);
}
else {
    $selectedValues = array_key_exists($value, $fieldOptions) ? [$fieldOptions[$value]] :  [];
}

$isComplex = is_array(array_first($selectedValues));
?>
<?php if ($isComplex): ?>
    <?php foreach ($selectedValues as $selectedValue): ?>
        <span class="list-selectable">
            <?php if (Html::isValidColor($selectedValue[1])): ?>
                <span class="status-indicator" style="background:<?= $selectedValue[1] ?>"></span>
            <?php elseif (strpos($selectedValue[1], '.')): ?>
                <img src="<?= $selectedValue[1] ?>" alt="" />
            <?php else: ?>
                <i class="<?= $selectedValue[1] ?>"></i>
            <?php endif ?>
            <?= e(__($selectedValue[0])) ?>
        </span>
    <?php endforeach ?>
<?php else: ?>
    <?= e(implode(', ', Arr::trans($selectedValues))) ?>
<?php endif ?>
