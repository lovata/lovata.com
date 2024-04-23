<?php if ($relationViewMode == 'single'): ?>
    <button
        class="btn btn-sm btn-secondary oc-icon-trash relation-button-delete"
        data-request="onRelationButtonDelete"
        data-request-confirm="<?= e($this->relationGetMessage('confirmDelete')) ?>"
        data-request-success="oc.relationBehavior.changed('<?= e($relationField) ?>', 'deleted')"
        data-stripe-load-indicator
    >
        <?= e($this->relationGetMessage('buttonDelete')) ?>
    </button>
<?php else: ?>
    <button
        class="btn btn-sm btn-secondary relation-button-delete"
        disabled="disabled"
        data-request="onRelationButtonDelete"
        data-request-confirm="<?= e($this->relationGetMessage('confirmDelete')) ?>"
        data-request-success="oc.relationBehavior.changed('<?= e($relationField) ?>', 'deleted')"
        data-list-checked-trigger
        data-list-checked-request
        data-stripe-load-indicator
    >
        <i class="icon-delete"></i> <?= e($this->relationGetMessage('buttonDeleteMany')) ?> <span data-list-checked-counter></span>
    </button>
<?php endif ?>
