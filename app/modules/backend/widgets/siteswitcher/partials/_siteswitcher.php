<?php if ($useMultisite): ?>
    <li class="mainmenu-item mainmenu-preview has-subitems <?= $editSite->is_styled ? 'has-solidicon' : '' ?>" data-submenu-index="sites">
        <a
            href="javascript:;"
            rel="noopener noreferrer"
        >
            <?php if ($editSite->is_styled): ?>
                <span class="nav-icon">
                    <span class="nav-colorpicker" style="--background-color:<?= e($editSite->color_background) ?>;--foreground-color:<?= e($editSite->color_foreground) ?>"></span>
                </span>
            <?php else: ?>
                <span class="nav-icon">
                    <i class="octo-icon-globe"></i>
                </span>
            <?php endif ?>

            <span class="nav-label">
                <?= e($editSite->name ?? '') ?>
            </span>
        </a>
    </li>
<?php elseif ($useAnySite): ?>
    <li class="mainmenu-item mainmenu-preview <?= $editSite->is_styled ? 'has-solidicon' : 'has-nolabel' ?>">
        <a
            href="<?= Url::to('/') ?>"
            target="_blank"
            rel="noopener noreferrer"
            <?php if (!$isVerticalMenu): ?>
                data-tooltip-text="<?= e(trans('backend::lang.tooltips.preview_website')) ?>"
            <?php endif ?>
        >
            <?php if ($editSite->is_styled): ?>
                <span class="nav-icon">
                    <span class="nav-colorpicker" style="--background-color:<?= e($editSite->color_background) ?>;--foreground-color:<?= e($editSite->color_foreground) ?>"></span>
                </span>
                <span class="nav-label">
                    <?= e($editSite->name ?? '') ?>
                </span>
            <?php else: ?>
                <span class="nav-icon">
                    <i class="octo-icon-location-target"></i>
                </span>
            <?php endif ?>

            <?php if ($isVerticalMenu && !$editSite->is_styled): ?>
                <span class="nav-label">
                    <?= e(trans('backend::lang.tooltips.preview_website')) ?>
                </span>
            <?php endif ?>
        </a>
    </li>
<?php endif ?>
