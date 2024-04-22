<div id="plugin-toolbar">
    <div data-control="toolbar">
        <a href="<?= Backend::url('system/updates') ?>" class="btn btn-default oc-icon-chevron-left">
            <?= e(__('Return to System Updates')) ?>
        </a>

        <div class="dropdown dropdown-fixed">
            <button
                type="button"
                class="btn btn-secondary dropdown-toggle"
                data-toggle="dropdown"
                data-list-checked-trigger>
                <?= __("Select Action...") ?>
            </button>

            <ul class="dropdown-menu">
                <li>
                    <a href="javascript:;"
                        data-request="onBulkAction"
                        data-request-data="action: 'disable'"
                        data-list-checked-request
                        data-request-confirm="<?= __("Are you sure you want to :action these plugins?", ['action' => __("disable")]) ?>"
                        data-stripe-load-indicator>
                        <i class="icon-ban"></i> <?= __("Disable Plugins") ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:;"
                        data-request="onBulkAction"
                        data-request-data="action: 'enable'"
                        data-list-checked-request
                        data-request-confirm="<?= __("Are you sure you want to :action these plugins?", ['action' => __("enable")]) ?>"
                        data-stripe-load-indicator>
                        <i class="icon-check"></i> <?= __("Enable Plugins") ?>
                    </a>
                </li>
                <?php if ($canUpdate): ?>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="javascript:;"
                            data-request="onBulkAction"
                            data-request-data="action: 'refresh'"
                            data-list-checked-request
                            data-request-confirm="<?= __("Are you sure you want to reset the selected plugins? This will reset each plugin's data, restoring it to the initial install state.") ?>"
                            data-stripe-load-indicator>
                            <i class="icon-bomb"></i> <?= __("Reset Plugin Data") ?>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</div>
