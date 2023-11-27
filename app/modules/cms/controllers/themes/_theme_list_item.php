<?php
    $author = $theme->getConfigValue('author');
?>

<div class="layout-cell min-height theme-thumbnail">
    <div class="thumbnail-container"><img src="<?= $theme->getPreviewImageUrl() ?>" alt="" /></div>
</div>
<div class="layout-cell min-height theme-description">
    <h3><?= e($theme->getConfigValue('name', $theme->getDirName())) ?></h3>
    <?php if (strlen($author)): ?>
        <p class="author"><?= __('By :name', ['name' => e($author)]) ?></p>
    <?php endif ?>
    <p class="description">
        <?= e($theme->getConfigValue('description', 'The theme description is not provided.')) ?>
    </p>
    <div class="controls">

        <?php if ($theme->isActiveTheme()): ?>
            <button
                type="submit"
                disabled
                class="btn btn-secondary btn-disabled">
                <i class="icon-star"></i>
                <?= __('Activate') ?>
            </button>
        <?php elseif (BackendAuth::userHasAccess('cms.themes.activate')): ?>
            <button
                type="submit"
                data-request="onSetActiveTheme"
                data-request-data="theme: '<?= e($theme->getDirName()) ?>'"
                data-stripe-load-indicator
                class="btn btn-primary">
                <i class="icon-check"></i>
                <?= __('Activate') ?>
            </button>
        <?php endif ?>
        <?php if (BackendAuth::userHasAccess('cms.theme_customize') && $theme->hasCustomData()): ?>
            <a
                href="<?= Backend::url('cms/themeoptions/update/'.$theme->getDirName()) ?>"
                class="btn btn-secondary">
                <i class="icon-paint-brush"></i>
                <?= __('Customize') ?>
            </a>
        <?php endif ?>
        <div class="dropdown">
            <button
                data-toggle="dropdown"
                class="btn btn-secondary">
                <i class="icon-wrench"></i>
                <?= __('Manage') ?>
            </button>
            <ul class="dropdown-menu" role="menu">
                <?php if (BackendAuth::userHasAccess('cms.themes.create')): ?>
                    <li role="presentation">
                        <a
                            role="menuitem"
                            tabindex="-1"
                            data-control="popup"
                            data-size="huge"
                            data-handler="onLoadFieldsForm"
                            data-request-data="theme: '<?= e($theme->getDirName()) ?>'"
                            href="javascript:;"
                        >
                            <i class="icon-pencil"></i>
                            <?= __('Edit Properties') ?>
                        </a>
                    </li>
                    <?php if ($theme->hasSeedContent()): ?>
                        <li role="presentation">
                            <a
                                role="menuitem"
                                tabindex="-1"
                                data-control="popup"
                                data-handler="onLoadSeedForm"
                                data-request-data="theme: '<?= e($theme->getDirName()) ?>'"
                                href="javascript:;"
                            >
                                <i class="icon-rocket"></i>
                                <?= __("Seed Content") ?>
                            </a>
                        </li>
                    <?php endif ?>
                    <li role="presentation">
                        <a
                            role="menuitem"
                            tabindex="-1"
                            data-control="popup"
                            data-handler="onLoadDuplicateForm"
                            data-request-data="theme: '<?= e($theme->getDirName()) ?>'"
                            href="javascript:;"
                        >
                            <i class="icon-copy"></i>
                            <?= __('Duplicate') ?>
                        </a>
                    </li>
                    <li role="presentation">
                        <a
                            role="menuitem"
                            tabindex="-1"
                            data-control="popup"
                            data-handler="onLoadImportForm"
                            data-request-data="theme: '<?= e($theme->getDirName()) ?>'"
                            href="javascript:;"
                        >
                            <i class="icon-upload"></i>
                            <?= __('Import') ?>
                        </a>
                    </li>
                <?php endif ?>
                <li role="presentation">
                    <a
                        role="menuitem"
                        tabindex="-1"
                        data-control="popup"
                        data-handler="onLoadExportForm"
                        data-request-data="theme: '<?= e($theme->getDirName()) ?>'"
                        href="javascript:;"
                    >
                        <i class="icon-download"></i>
                        <?= __('Export') ?>
                    </a>
                </li>
                <?php if (!$theme->isActiveTheme() && BackendAuth::userHasAccess('cms.themes.delete')): ?>
                    <li role="presentation" class="divider"></li>
                    <li role="presentation">
                        <a
                            role="menuitem"
                            tabindex="-1"
                            data-request="onDelete"
                            data-request-confirm="<?= __('Delete this theme? It cannot be undone!') ?>"
                            data-request-data="theme: '<?= e($theme->getDirName()) ?>'"
                            href="javascript:;"
                        >
                            <i class="icon-trash"></i>
                            <?= __('Delete') ?>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</div>
