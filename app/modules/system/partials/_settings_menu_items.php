<?php
    $context = System\Classes\SettingsManager::instance()->getContext();

    $collapsedGroups = explode('|',
        isset($_COOKIE['sidenav_treegroupStatus']) ? $_COOKIE['sidenav_treegroupStatus'] : null
    );

    $settingsMenuItemIsActive = function($item, $context)  {
        return strtolower($item->owner) == $context->owner && strtolower($item->code) == $context->itemCode;
    };

    $settingsMenuItemsIsActive = function($items, $context) use ($settingsMenuItemIsActive) {
        foreach ($items as $item) {
            if ($settingsMenuItemIsActive($item, $context)) {
                return true;
            }
        }

        return false;
    };
?>
<ul class="top-level">
    <?php foreach ($items as $category => $items): ?>
        <?php
            $collapsed = in_array($category, $collapsedGroups);
        ?>
        <li class="<?= $settingsMenuItemsIsActive($items, $context) ? 'is-active-group' : 'is-inactive-group' ?>" data-group-code="<?= e($category) ?>" <?= $collapsed ? 'data-status="collapsed"' : null ?>>
            <div class="group">
                <h3><?= e(__($category)) ?></h3>
            </div>

            <ul <?= $collapsed ? 'style="overflow: visible; height: 0px; display: none;"' : null ?>>
                <?php foreach ($items as $item): ?>
                    <li
                        class="<?= strtolower($item->owner) == $context->owner && strtolower($item->code) == $context->itemCode ? 'active' : false ?>"
                        data-keywords="<?= e(__($item->keywords ?? '')) ?>"
                        <?= Html::attributes($item->attributes) ?>
                    >
                        <a href="<?= $item->url ?>" ontouchstart="">
                            <i class="<?= $item->icon ?? '' ?>"></i>
                            <span class="header"><?= e(__($item->label ?? '')) ?></span>
                            <span class="description"><?= e(__($item->description ?? '')) ?></span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </li>
    <?php endforeach ?>
</ul>
