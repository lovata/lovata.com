<?php if (is_array($value) && count($value) === 2): ?>
    <span
        class="list-colorpicker is-twotone"
        style="--background-color:<?= e($value[0]) ?>;--foreground-color:<?= e($value[1]) ?>"
    ><span>
<?php elseif ($value): ?>
    <span
        class="list-colorpicker"
        style="--background-color:<?= e($value) ?>"
    ><span>
<?php endif ?>
