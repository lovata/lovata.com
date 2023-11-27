<div id="plugin-toolbar">
    <div data-control="toolbar">
        <a href="<?= Backend::url('system/updates') ?>" class="btn btn-default oc-icon-chevron-left">
            <?= e(__('Return to System Updates')) ?>
        </a>

        <div class="btn-group dropdown dropdown-fixed">
            <button
                type="button"
                class="btn btn-default dropdown-toggle"
                data-toggle="dropdown"
                data-list-checked-trigger>
                <?= e(trans('system::lang.plugins.select_label')) ?>
            </button>

            <ul class="dropdown-menu">
                <li>
                    <a href="javascript:;"
                        data-request="onBulkAction"
                        data-request-data="action: 'disable'"
                        data-list-checked-request
                        data-request-confirm="<?= e(trans('system::lang.plugins.action_confirm', ['action' => e(trans('system::lang.plugins.disable'))])) ?>"
                        data-stripe-load-indicator>
                        <i class="icon-ban"></i> <?= e(trans('system::lang.plugins.disable_label')) ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:;"
                        data-request="onBulkAction"
                        data-request-data="action: 'enable'"
                        data-list-checked-request
                        data-request-confirm="<?= e(trans('system::lang.plugins.action_confirm', ['action' => e(trans('system::lang.plugins.enable'))])) ?>"
                        data-stripe-load-indicator>
                        <i class="icon-check"></i> <?= e(trans('system::lang.plugins.enable_label')) ?>
                    </a>
                </li>
                <?php if ($canUpdate): ?>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="javascript:;"
                            data-request="onBulkAction"
                            data-request-data="action: 'refresh'"
                            data-list-checked-request
                            data-request-confirm="<?= e(trans('system::lang.plugins.refresh_confirm')) ?>"
                            data-stripe-load-indicator>
                            <i class="icon-bomb"></i> <?= e(trans('system::lang.plugins.refresh_label')) ?>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</div>
