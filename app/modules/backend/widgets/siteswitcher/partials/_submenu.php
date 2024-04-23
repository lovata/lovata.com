<?php if (Site::hasMultiEditSite()): ?>
    <ul
        class="mainmenu-items mainmenu-submenu-dropdown hover-effects siteswitcher-dropdown"
        data-submenu-index="sites"
        data-control="siteswitcher">
        <?php if (Site::hasSiteGroups()): ?>
            <?= $this->makePartial('submenu_grouped_items') ?>
        <?php else: ?>
            <li class="mainmenu-item section-title">
                <span class="nav-label"><?= __("Selected Site") ?></span>
            </li>
            <?= $this->makePartial('submenu_items') ?>
        <?php endif ?>
        <?= $this->makePartial('submenu_footer') ?>
    </ul>
<?php endif ?>
