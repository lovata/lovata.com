<?php
    $statusCode = $this->listGetFilterWidget()->getScope('status_code')->value;
?>
<?php if ($statusCode === 'deleted'): ?>
    <?php if ($this->hasSourcePermission('delete')): ?>
        <button
            type="button"
            class="btn btn-secondary oc-icon-angle-down"
            data-toggle="dropdown"
            data-list-checked-trigger
        >
            <?= __("Select Action") ?>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a
                    href="javascript:;"
                    class="dropdown-item"
                    data-request="onBulkAction"
                    data-request-data="action: 'restore'"
                    data-list-checked-request
                    data-request-confirm="<?= __("Are you sure?") ?>"
                    data-stripe-load-indicator>
                    <i class="octo-icon-refresh"></i> <?= __("Restore") ?>
                </a>
            </li>
            <li>
                <a
                    href="javascript:;"
                    class="dropdown-item"
                    data-request="onBulkAction"
                    data-request-data="action: 'forceDelete'"
                    data-list-checked-request
                    data-request-confirm="<?= __("Are you sure?") ?>"
                    data-stripe-load-indicator>
                    <i class="octo-icon-delete"></i> <?= __("Delete Forever") ?>
                </a>
            </li>
        </ul>
    <?php endif ?>
<?php else: ?>
    <button
        type="button"
        class="btn btn-secondary oc-icon-angle-down"
        data-toggle="dropdown"
        data-list-checked-trigger
    >
        <?= __("Select Action") ?>
    </button>
    <ul class="dropdown-menu">
        <?php if ($this->hasSourcePermission('publish')): ?>
            <li>
                <a
                    href="javascript:;"
                    class="dropdown-item"
                    data-request="onBulkAction"
                    data-request-data="action: 'disable'"
                    data-list-checked-request
                    data-request-confirm="<?= __("Are you sure?") ?>"
                    data-stripe-load-indicator>
                    <i class="oc-icon-ban"></i> <?= __("Disable") ?>
                </a>
            </li>
            <li>
                <a
                    href="javascript:;"
                    class="dropdown-item"
                    data-request="onBulkAction"
                    data-request-data="action: 'enable'"
                    data-list-checked-request
                    data-request-confirm="<?= __("Are you sure?") ?>"
                    data-stripe-load-indicator>
                    <i class="oc-icon-check"></i> <?= __("Enable") ?>
                </a>
            </li>
            <li>
                <a
                    href="javascript:;"
                    class="dropdown-item"
                    data-request="onBulkAction"
                    data-request-data="action: 'duplicate'"
                    data-list-checked-request
                    data-request-confirm="<?= __("Are you sure?") ?>"
                    data-stripe-load-indicator>
                    <i class="oc-icon-copy"></i> <?= __("Duplicate") ?>
                </a>
            </li>
        <?php endif ?>
        <?php if ($this->hasSourcePermission('delete')): ?>
            <li role="separator" class="divider"></li>
            <li>
                <a
                    href="javascript:;"
                    class="dropdown-item"
                    data-request="onBulkAction"
                    data-request-data="action: 'delete'"
                    data-list-checked-request
                    data-request-confirm="<?= __("Are you sure?") ?>"
                    data-stripe-load-indicator>
                    <i class="oc-icon-bomb"></i> <?= __("Delete") ?>
                </a>
            </li>
        <?php endif ?>
    </ul>
<?php endif ?>
