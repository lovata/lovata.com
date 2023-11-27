<div data-control="toolbar">
    <?php if (!$projectDetails): ?>
        <a
            href="javascript:;"
            class="btn btn-primary oc-icon-bolt"
            data-control="popup"
            data-handler="onLoadProjectForm"
        >
            <?= e(__('Register Software')) ?>
    <?php else: ?>
        </a>
        <a
            href="javascript:;"
            class="btn btn-primary oc-icon-refresh"
            data-control="popup"
            data-handler="<?= $this->updaterWidget->getEventHandler('onLoadUpdates') ?>"
        >
            <?= e(__('Check For Updates')) ?>
        </a>
        <a
            href="<?= Backend::url('system/market') ?>"
            class="btn btn-default oc-icon-plus">
            <?= e(__('Install Packages')) ?>
        </a>
    <?php endif ?>
    <?php if (System::hasModule('Cms')): ?>
        <a
            href="<?= Backend::url('cms/themes') ?>"
            class="btn btn-default oc-icon-image">
            <?= e(__('Manage Themes')) ?>
        </a>
    <?php endif ?>
    <a
        href="<?= Backend::url('system/updates/manage') ?>"
        class="btn btn-default oc-icon-puzzle-piece">
        <?= e(__('Manage Plugins')) ?>
    </a>
</div>
