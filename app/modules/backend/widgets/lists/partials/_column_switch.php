<span class="list-switch <?= $value ? 'is-true' : 'is-false' ?>">
    <?php if ($value): ?>
        <i class="icon-check" data-bs-toggle="tooltip" title="<?= e($trueValue) ?>"></i>
    <?php else: ?>
        <i class="icon-times" data-bs-toggle="tooltip" title="<?= e($falseValue) ?>"></i>
    <?php endif ?>
</span>