<?php
    $statusCode = $this->listGetFilterWidget()->getScope('status_code')->value;
?>
<?php if ($statusCode === 'deleted'): ?>
    <?php if ($this->hasSourcePermission('delete')): ?>
        <?= Ui::ajaxButton("Restore", 'onBulkAction')
            ->ajaxData(['action' => 'restore'])
            ->confirmMessage("Are you sure?")
            ->listCheckedTrigger()
            ->listCheckedRequest()
            ->icon('icon-refresh')
            ->secondary() ?>

        <?= Ui::ajaxButton("Delete Forever", 'onBulkAction')
            ->ajaxData(['action' => 'forceDelete'])
            ->confirmMessage("Are you sure?")
            ->listCheckedTrigger()
            ->listCheckedRequest()
            ->icon('icon-delete')
            ->secondary() ?>
    <?php endif ?>
<?php else: ?>
    <?php if ($this->hasSourcePermission('publish')): ?>
        <div class="dropdown dropdown-fixed">
            <button
                type="button"
                class="btn btn-secondary oc-icon-angle-down"
                data-toggle="dropdown"
                data-list-checked-trigger
            ><?= __("Change Status") ?></button>
            <ul class="dropdown-menu">
                <li>
                    <?= Ui::ajaxButton("Enable", 'onBulkAction')
                        ->ajaxData(['action' => 'enable'])
                        ->confirmMessage("Are you sure?")
                        ->listCheckedRequest()
                        ->icon('icon-check')
                        ->replaceCssClass('dropdown-item')
                        ->secondary() ?>
                    </a>
                </li>
                <li>
                    <?= Ui::ajaxButton("Disable", 'onBulkAction')
                        ->ajaxData(['action' => 'disable'])
                        ->confirmMessage("Are you sure?")
                        ->listCheckedRequest()
                        ->icon('icon-ban')
                        ->replaceCssClass('dropdown-item')
                        ->secondary() ?>
                </li>
            </ul>
        </div>
        <?= Ui::ajaxButton("Duplicate", 'onBulkAction')
            ->ajaxData(['action' => 'duplicate'])
            ->confirmMessage("Are you sure?")
            ->listCheckedTrigger()
            ->listCheckedRequest()
            ->icon('icon-copy')
            ->secondary() ?>
    <?php endif ?>
    <?php if ($this->hasSourcePermission('delete')): ?>
        <?= Ui::ajaxButton("Delete", 'onBulkAction')
            ->ajaxData(['action' => 'delete'])
            ->confirmMessage("Are you sure?")
            ->listCheckedTrigger()
            ->listCheckedRequest()
            ->icon('icon-delete')
            ->secondary() ?>
    <?php endif ?>
<?php endif ?>
