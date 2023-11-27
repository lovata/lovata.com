<?php
    $context = BackendMenu::getContext();
    $sideMenuItems = BackendMenu::listMainMenuSubItems();
?>
<?php if ($sideMenuItems): ?>
    <div class="control-toolbar responsive-sidebar-toolbar" role="navigation">
        <div data-control="toolbar">
            <nav class="layout-sidenav sidenav-responsive">
                <ul class="mainmenu-items">
                    <?= $this->makeLayoutPartial('submenu_items', [
                        'sideMenuItems' => $sideMenuItems,
                        'mainMenuItemActive' => true,
                        'mainMenuItemCode' => $context->mainMenuCode,
                        'noSvgEffects' => true,
                        'context' => $context
                    ]) ?>
                </ul>
            </nav>
        </div>
    </div>
<?php endif ?>
