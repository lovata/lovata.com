<div data-control="toolbar">
    <?php if (!$projectDetails): ?>
        <?= Ui::popupButton("Register Software", 'onLoadProjectForm')
            ->primary()
            ->icon('icon-bolt')
        ?>
    <?php else: ?>
        <?= Ui::popupButton("Check For Updates", $this->updaterWidget->getEventHandler('onLoadUpdates'))
            ->primary()
            ->icon('icon-refresh')
        ?>
        <?= Ui::button("Install Packages", 'system/market')
            ->icon('icon-plus')
            ->secondary() ?>
    <?php endif ?>
    <?php if (System::hasModule('Cms')): ?>
        <?= Ui::button("Manage Themes", 'cms/themes')
            ->icon('icon-image')
            ->secondary() ?>
    <?php endif ?>
        <?= Ui::button("Manage Plugins", 'system/updates/manage')
            ->icon('icon-puzzle-piece')
            ->secondary() ?>
</div>
