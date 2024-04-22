<?php
    $action = $record->is_disabled ? 'enable' : 'disable';
?>
<div class="form-check form-switch nolink">
    <input
        class="form-check-input"
        data-request="onBulkAction"
        data-request-data="action: '<?= $action ?>', checked: [<?= $record->id ?>]"
        data-request-update="list_manage_toolbar: '#plugin-toolbar'"
        type="checkbox"
        name="disable_<?= $record->id ?>"
        value="<?= !$record->is_disabled ?>"
        <?php if (!$record->is_disabled): ?>checked<?php endif ?>
        data-stripe-load-indicator
    >
</div>
