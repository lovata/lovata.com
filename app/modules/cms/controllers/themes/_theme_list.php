<?php foreach ($themes as $index => $theme): ?>

    <div id="themeListItem-<?= $theme->getId() ?>" class="d-flex theme-item <?= $theme->isActiveTheme() ? 'active' : null ?>">
        <?= $this->makePartial('theme_list_item', ['theme' => $theme]) ?>
    </div>

<?php endforeach ?>

<?php if (BackendAuth::userHasAccess('cms.themes.create')): ?>
    <div class="d-flex theme-item theme-item-links">
        <div class="theme-thumbnail">
            <!-- Spacer -->
        </div>
        <div class="theme-description">
            <a
                class="create-new-theme"
                data-control="popup"
                data-handler="onLoadCreateForm"
                data-size="huge"
                href="javascript:;">
                <?= __('Create a New Blank Theme') ?>
            </a>
            <a
                class="find-more-themes"
                href="<?= Backend::url('system/market/index/themes') ?>">
                <?= __('Find More Themes') ?>
            </a>
        </div>
    </div>
<?php endif ?>
