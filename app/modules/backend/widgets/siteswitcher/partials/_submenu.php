<?php if (Site::hasMultiEditSite()): ?>
    <ul
        class="mainmenu-items mainmenu-submenu-dropdown hover-effects siteswitcher-dropdown"
        data-submenu-index="sites"
        data-control="siteswitcher">
        <li class="mainmenu-item section-title">
            <span class="nav-label"><?= __("Selected Site") ?></span>
        </li>
        <?= $this->makePartial('submenu_items') ?>
    </ul>
<?php endif ?>
