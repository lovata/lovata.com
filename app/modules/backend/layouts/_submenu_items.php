<?php foreach ($sideMenuItems as $sideItemCode => $item): ?>
    <?php if ($item->itemType === 'section'): ?>
        <li
            class="mainmenu-item section-title" <?= $item->itemAttributes() ?>>
            <span class="nav-label">
                <?= e(__($item->label)) ?>
            </span>
        </li>
    <?php elseif ($item->itemType === 'ruler'): ?>
        <li class="mainmenu-item divider" <?= $item->itemAttributes() ?>></li>
    <?php else: ?>
        <li
            class="
                mainmenu-item
                svg-icon-container
                <?= !isset($noSvgEffects) ? 'svg-active-effects' : '' ?>
                <?= $mainMenuItemActive && BackendMenu::isSideMenuItemActive($item) ? 'active' : null ?>
                <?= $item->itemType == 'primary' ? 'sidebar-button' : null ?>
                <?= $item->counter ? 'has-counter' : '' ?>
            "
            <?= $item->itemAttributes() ?>
        >
            <a href="<?= $item->url ?>" <?= $item->linkAttributes() ?>>
                <?= $this->makeLayoutPartial('mainmenu_item', [
                    'item' => $item,
                    'isSubmenu' => true,
                    'mainMenuItemCode' => $mainMenuItemCode
                ]) ?>
            </a>
        </li>
    <?php endif ?>
<?php endforeach ?>
