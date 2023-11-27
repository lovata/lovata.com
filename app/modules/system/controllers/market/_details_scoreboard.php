<div class="scoreboard">
    <div data-control="toolbar">
        <div class="scoreboard-item title-value">
            <?php if ($product->isTheme): ?>
                <h4><?= e(trans('system::lang.theme.label')) ?></h4>
            <?php else: ?>
                <h4><?= e(trans('system::lang.plugin.label')) ?></h4>
            <?php endif ?>
            <p class="oc-<?= $product->icon ?>"><?= e(__($product->name)) ?></p>
            <p class="description">
                <?= e($product->code) ?>
            </p>
        </div>
        <div class="scoreboard-item title-value">
            <h4><?= e(trans('system::lang.updates.details_author')) ?></h4>
            <p><?= e(__($product->author)) ?></p>
            <p class="description">
                <?php if ($product->homepage): ?>
                    <a href="<?= e($product->homepage) ?>" target="_blank">
                        <?= e(trans('system::lang.updates.details_view_homepage')) ?>
                    </a>
                <?php else: ?>
                    <a href="<?= e($product->isTheme ? 'https://octobercms.com/themes' : 'https://octobercms.com/plugins') ?>" target="_blank">
                        <?= e(trans('system::lang.updates.details_view_homepage')) ?>
                    </a>
                <?php endif ?>
            </p>
        </div>
        <?php if ($product->currentVersion): ?>
            <div class="scoreboard-item title-value">
                <h4><?= e(trans('system::lang.updates.details_current_version')) ?></h4>
                <p><?= e($product->currentVersion) ?></p>
                <p class="description">
                    <a
                        href="javascript:;"
                        data-control="popup"
                        data-handler="<?= $this->changelogWidget->getEventHandler('onLoadPluginChangelog') ?>"
                        data-request-data="code: '<?= e($product->code) ?>'"
                    >
                        <?= e(__('View Changelog')) ?>
                    </a>
                </p>
            </div>
        <?php endif ?>
    </div>
</div>
