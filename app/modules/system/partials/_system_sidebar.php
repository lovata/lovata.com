<?php Block::put('layout-top-row') ?>
    <div>
        <a
            class="system-home-link back-link-other"
            href="<?= Backend::url('system/settings') ?>"
            onclick="return sideNavSettingsHomeClick()">
            <i></i><?= __('Show All Settings') ?>
        </a>
    </div>
<?php Block::endPut() ?>

<div
    class="sidenav-tree"
    data-control="sidenav-tree"
    data-search-input="#settings-search-input">

    <div class="d-flex flex-column sidenav-tree-content h-100">
        <div>
            <a class="system-home-link" href="<?= Backend::url('system/settings') ?>">
                <i class="icon-home"></i><?= __('Show All Settings') ?>
            </a>
        </div>
        <div>
            <?= $this->makePartial('~/modules/system/partials/_settings_menu_toolbar.php') ?>
        </div>

        <div class="flex-grow-1">
            <div class="position-relative h-100">
                <div class="sidenav-tree-scroll-canvas">
                    <div class="control-scrollbar" data-control="scrollbar">
                        <?= $this->makePartial('~/modules/system/partials/_settings_menu.php') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
