<?php if ($canManageSite || $useAnySite): ?>
    <li class="mainmenu-item divider"></li>
<?php endif ?>
<?php if ($canManageSite): ?>
<li class="mainmenu-item">
    <a href="<?= Backend::url('system/sites') ?>">
        <span class="nav-icon">
            <i class="icon-globe-site"></i>
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
                <i class="icon-location-target"></i>
            </span>
            <span class="nav-label">
                <?= __("Preview the Website") ?>
            </span>
        </a>
    </li>
<?php endif ?>
