<span class="nav-icon">
    <?php if ($item->iconSvg): ?>
        <img
            src="<?= Url::asset($item->iconSvg) ?>"
            class="svg-icon"
            loading="lazy"
        />
    <?php else: ?>
        <i class="<?= $item->iconSvg ? 'svg-replace' : '' ?> <?= $item->icon ?>"></i>
    <?php endif ?>
</span>
<span class="nav-label">
    <?= e(__($item->label)) ?>
</span>
<?php if (!isset($noCounter)): ?>
    <span
        class="counter <?= !$item->counter ? 'empty' : '' ?>"
        data-menu-id="<?= !isset($isSubmenu) ? e($item->code) : e($mainMenuItemCode.'/'.$item->code) ?>"
        <?php if ($item->counterLabel): ?>title="<?= e(__($item->counterLabel)) ?>"<?php endif ?>
    ><?= e($item->counter) ?></span>
<?php endif?>
