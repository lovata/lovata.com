<?php Block::put('breadcrumb') ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Backend::url('system/updates') ?>"><?= e(__('System Updates')) ?></a></li>
        <li class="breadcrumb-item"><a href="<?= Backend::url('system/market') ?>"><?= e(__('Marketplace')) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
    </ol>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <div class="mw-950">
        <?php if ($warnings = $this->updaterWidget->renderWarnings()): ?>
            <?= $warnings ?>
        <?php endif ?>

        <div class="row">
            <div class="col-sm-9">
                <?= $this->makePartial('details_scoreboard') ?>
            </div>
            <div class="col-sm-3">
                <?= $this->makePartial('details_toolbar') ?>
            </div>
        </div>

        <div class="control-tabs primary-tabs" data-control="tab">
            <ul class="nav nav-tabs">
                <li class="<?= $activeTab == 'readme' ? 'active' : '' ?>">
                    <a
                        href="#readme"
                        data-tab-url="<?= Backend::url('system/market/theme/'.$urlCode.'/readme') ?>">
                        <?= e(__('Documentation')) ?>
                    </a>
                </li>
                <?php if ($product->upgradeHtml): ?>
                    <li class="<?= $activeTab == 'upgrades' ? 'active' : '' ?>">
                        <a
                            href="#upgrades"
                            data-tab-url="<?= Backend::url('system/market/theme/'.$urlCode.'/upgrades') ?>">
                            <?= e(__('Upgrade Guide')) ?>
                        </a>
                    </li>
                <?php endif ?>
                <?php if ($product->licenseHtml): ?>
                    <li class="<?= $activeTab == 'license' ? 'active' : '' ?>">
                        <a
                            href="#license"
                            data-tab-url="<?= Backend::url('system/market/theme/'.$urlCode.'/license') ?>">
                            <?= e(__('License')) ?>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
            <div class="tab-content">
                <div class="tab-pane <?= $activeTab == 'readme' ? 'active' : '' ?>">
                    <div class="plugin-details-content">
                        <?php if ($product->contentHtml): ?>
                            <?= $product->contentHtml ?>
                        <?php else: ?>
                            <?= Ui::callout(function() { ?>
                                <p><?= e(__('There is no documentation provided.')) ?></p>
                            <?php }) ?>
                        <?php endif ?>
                    </div>
                </div>
                <?php if ($product->upgradeHtml): ?>
                    <div class="tab-pane <?= $activeTab == 'upgrades' ? 'active' : '' ?>">
                        <div class="plugin-details-content">
                            <?= $product->upgradeHtml ?>
                        </div>
                    </div>
                <?php endif ?>
                <?php if ($product->licenseHtml): ?>
                    <div class="tab-pane <?= $activeTab == 'license' ? 'active' : '' ?>">
                        <div class="plugin-details-content">
                            <?= $product->licenseHtml ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>

    </div>

<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= Backend::url('system/updates') ?>" class="btn btn-default"><?= __('Return to System Settings') ?></a></p>

<?php endif ?>
