<?php if ($relationViewMode == 'single'): ?>
    <button
        class="btn btn-sm btn-secondary oc-icon-minus relation-button-remove"
        data-request="onRelationButtonRemove"
        data-request-success="oc.relationBehavior.changed('<?= e($relationField) ?>', 'removed')"
        data-stripe-load-indicator
    >
        <?= e($this->relationGetMessage('buttonRemove')) ?>
    </button>
<?php else: ?>
    <button
        class="btn btn-sm btn-secondary relation-button-remove"
        disabled="disabled"
        data-request="onRelationButtonRemove"
        data-request-success="oc.relationBehavior.changed('<?= e($relationField) ?>', 'removed')"
        data-list-checked-trigger
        data-list-checked-request
        data-stripe-load-indicator
    >
        <i class="icon-list-remove"></i> <?= e($this->relationGetMessage('buttonRemoveMany')) ?> <span data-list-checked-counter></span>
    </button>
<?php endif ?>
