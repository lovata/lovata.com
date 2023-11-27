<div class="dropdown dropup">
    <a
        href="javascript:;"
        class="manage-widgets"
        data-toggle="dropdown">
        <i class="icon-cogs"></i> <?= __('Manage Widgets') ?>
    </a>

    <ul class="dropdown-menu" role="menu">
        <li role="presentation">
            <a
                role="menuitem"
                href="javascript:;"
                class="dropdown-item"
                data-control="popup"
                data-handler="<?= $this->getEventHandler('onLoadAddPopup') ?>"
                tabindex="-1">
                <i class="icon-plus"></i>
                <?= __('Add Widget') ?>
            </a>
        </li>
        <li role="separator" class="dropdown-divider"></li>
        <?php if ($this->showMakeDefault): ?>
            <li role="presentation">
                <a
                    role="menuitem"
                    href="javascript:;"
                    class="dropdown-item"
                    data-request="<?= $this->getEventHandler('onMakeLayoutDefault') ?>"
                    data-request-confirm="<?= __('Set the current layout as the default?') ?>"
                    tabindex="-1">
                    <i class="icon-floppy-o"></i>
                    <?= __('Make Default') ?>
                </a>
            </li>
        <?php endif ?>
        <li role="presentation">
            <a
                role="menuitem"
                href="javascript:;"
                class="dropdown-item"
                data-request-success="$(window).trigger('oc.reportWidgetRefresh')"
                data-request="<?= $this->getEventHandler('onResetWidgets') ?>"
                data-request-confirm="<?= __('Reset layout back to default?') ?>"
                tabindex="-1">
                <i class="icon-repeat"></i>
                <?= __('Reset Layout') ?>
            </a>
        </li>
    </ul>
</div>
