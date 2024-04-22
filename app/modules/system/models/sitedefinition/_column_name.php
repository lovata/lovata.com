<span class="t-nw">
    <strong><?= e($value) ?></strong>
    <?php if ($record->is_primary): ?>
        <i class="icon-shield small" title="<?= __("Primary Site") ?>"></i>
    <?php endif ?>
</span>
