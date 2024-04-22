<?php if ($relationViewMode == 'single'): ?>
    <button
        class="btn btn-sm btn-secondary relation-button-unlink"
        data-request="onRelationButtonUnlink"
        data-request-success="oc.relationBehavior.changed('<?= e($relationField) ?>', 'removed')"
        data-request-confirm="<?= e($this->relationGetMessage('confirmUnlink')) ?>"
        data-stripe-load-indicator
    >
        <i class="icon-unlink"></i> <?= e($this->relationGetMessage('buttonUnlink')) ?>
    </button>
<?php else: ?>
    <button
        class="btn btn-sm btn-secondary relation-button-unlink"
        disabled="disabled"
        data-request="onRelationButtonUnlink"
        data-request-success="oc.relationBehavior.changed('<?= e($relationField) ?>', 'removed')"
        data-request-confirm="<?= e($this->relationGetMessage('confirmUnlink')) ?>"
        data-list-checked-trigger
        data-list-checked-request
        data-stripe-load-indicator
    >
        <i class="icon-unlink"></i> <?= e($this->relationGetMessage('buttonUnlinkMany')) ?> <span data-list-checked-counter></span>
    </button>
<?php endif ?>
