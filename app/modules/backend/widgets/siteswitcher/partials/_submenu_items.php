<?php
    $editSiteId = $editSite ? $editSite->id : null;
?>
<?php foreach ($sites as $site): ?>
    <li class="mainmenu-item has-bullet has-flag <?= $site->id === $editSiteId ? 'is-selected' : '' ?>">
        <a
            href="?<?= http_build_query(array_merge(get(), ['_site_id' => e($site->id)])) ?>"
            <?php if ($switchHandler): ?>
                data-handler="<?= $switchHandler ?>"
                data-request-data="site_id: '<?= e($site->id) ?>'"
            <?php endif ?>
            data-siteswitcher-link>
            <span class="nav-label">
                <?= e($site->name) ?>
            </span>
            <?php if ($flag = $site->flag_icon): ?>
                <span class="nav-icon nav-icon-flag">
                    <i class="<?= e($flag) ?>"></i>
                </span>
            <?php endif ?>
        </a>
    </li>
<?php endforeach ?>
<?php if ($canManageSite || $useAnySite): ?>
    <li class="mainmenu-item divider"></li>
<?php endif ?>
<?php if ($canManageSite): ?>
<li class="mainmenu-item">
    <a href="<?= Backend::url('system/sites') ?>">
        <span class="nav-icon">
            <i class="octo-icon-globe"></i>
        </span>
        <span class="nav-label">
            <?= __("Manage Sites") ?>
        </span>
    </a>
</li>
<?php endif ?>
<?php if ($useAnySite): ?>
    <li class="mainmenu-item">
        <a href="<?= $editSite->base_url ?>"
            target="_blank"
            rel="noopener noreferrer">
            <span class="nav-icon">
                <i class="octo-icon-location-target"></i>
            </span>
            <span class="nav-label">
                <?= e(trans('backend::lang.tooltips.preview_website')) ?>
            </span>
        </a>
    </li>
<?php endif ?>
