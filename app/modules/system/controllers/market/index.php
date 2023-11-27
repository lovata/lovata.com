<?php Block::put('breadcrumb') ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Backend::url('system/updates') ?>"><?= __("System Updates") ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
    </ol>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <?php if ($warnings = $this->updaterWidget->renderWarnings()): ?>
        <?= $warnings ?>
    <?php endif ?>

    <div class="control-tabs content-tabs" data-control="tab">

        <div style="position:absolute;top:6px;right:10px;z-index:2">
            <a
                href="javascript:;"
                data-control="popup"
                data-handler="<?= $this->updaterWidget->getEventHandler('onSyncProject') ?>"
                class="btn btn-primary oc-icon-refresh">
                <?= __("Sync Project") ?>
            </a>
        </div>

        <ul class="nav nav-tabs">
            <?php if ($projectDetails): ?>
                <li class="<?= $activeTab == 'project' ? 'active' : '' ?>">
                    <a
                        href="#tabProject"
                        data-tab-url="<?= Backend::url('system/market/index/project') ?>">
                        <?= __("Project") ?>
                    </a>
                </li>
            <?php endif ?>
            <?php /*
            <li class="<?= $activeTab == 'recommended' ? 'active' : '' ?>">
                <a
                    href="#tabRecommended"
                    data-tab-url="<?= Backend::url('system/market/index/recommended') ?>">
                    <?= __("Recommended") ?>
                </a>
            </li>
            */ ?>
            <li class="<?= $activeTab == 'plugins' ? 'active' : '' ?>">
                <a
                    href="#tabPlugins"
                    data-tab-url="<?= Backend::url('system/market/index/plugins') ?>">
                    <?= __("Plugins") ?>
                </a>
            </li>
            <li class="<?= $activeTab == 'themes' ? 'active' : '' ?>">
                <a
                    href="#tabThemes"
                    data-tab-url="<?= Backend::url('system/market/index/themes') ?>">
                    <?= e(trans('system::lang.updates.themes')) ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <?php if ($projectDetails): ?>
                <div class="tab-pane pane-bordered <?= $activeTab == 'project' ? 'active' : '' ?>">
                    <div class="padded-container">
                        <?= $this->makePartial('manage_project') ?>
                    </div>
                </div>
            <?php endif ?>
            <?php /*
            <div class="tab-pane pane-bordered <?= $activeTab == 'recommended' ? 'active' : '' ?>">
                <div class="padded-container">
                    <?= $this->makePartial('install_recommended') ?>
                </div>
            </div>
            */ ?>
            <div class="tab-pane pane-bordered <?= $activeTab == 'plugins' ? 'active' : '' ?>">
                <div class="padded-container">
                    <?= $this->makePartial('install_plugins') ?>
                </div>
            </div>
            <div class="tab-pane pane-bordered <?= $activeTab == 'themes' ? 'active' : '' ?>">
                <div class="padded-container">
                    <?= $this->makePartial('install_themes') ?>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= Backend::url('system/updates') ?>" class="btn btn-default"><?= __("Return to System Settings") ?></a></p>

<?php endif ?>
