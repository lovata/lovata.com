<?php
$installHandler = $product->isTheme
    ? $this->updaterWidget->getEventHandler('onInstallThemeCheck')
    : $this->updaterWidget->getEventHandler('onInstallPlugin');

$removeHandler = $product->isTheme
    ? $this->updaterWidget->getEventHandler('onRemoveTheme')
    : $this->updaterWidget->getEventHandler('onRemovePlugin');
?>
<div class="text-sm-end text-center">
    <div class="action-button-wrapper">
        <?php if ($projectDetails): ?>
            <?php if (!$product->installed()): ?>
                <?php if ($product->canInstall): ?>
                    <a
                        href="javascript:;"
                        data-control="popup"
                        data-handler="<?= $installHandler ?>"
                        data-request-data="code: '<?= e($product->code) ?>'"
                        class="btn btn-success">
                        <i class="icon-plus"></i>
                        <?= __("Install") ?>
                    </a>
                <?php elseif ($product->canResetData): ?>
                    <a
                        href="javascript:;"
                        data-request="onResetProductData"
                        data-request-confirm="<?= __("Are you sure?") ?>"
                        data-request-data="code: '<?= e($product->code) ?>'"
                        class="btn btn-danger">
                        <i class="icon-bomb"></i>
                        <?= __("Remove Data") ?>
                    </a>
                <?php else: ?>
                    <a
                        href="<?= e($product->homepage) ?>"
                        target="_blank"
                        rel="nofollow"
                        class="btn btn-success">
                        <i class="icon-external-link"></i>
                        <?= __("Buy Now") ?>
                    </a>
                <?php endif ?>
            <?php else: ?>
                <a
                    href="javascript:;"
                    data-control="popup"
                    data-handler="<?= $removeHandler ?>"
                    data-request-confirm="<?= __("Are you sure?") ?>"
                    data-request-data="code: '<?= e($product->code) ?>'"
                    class="btn btn-danger">
                    <i class="icon-chain-broken"></i>
                    <?= __("Remove") ?>
                </a>
                <?php /*
                <a
                    href="<?= Backend::url('system/updates/manage') ?>"
                    class="btn btn-default oc-icon-cog">
                    <?= __("Manage") ?>
                </a>
                */ ?>
            <?php endif ?>
        <?php else: ?>
            <a
                href="javascript:;"
                class="btn btn-success disabled">
                <i class="icon-plus"></i>
                <?= __("Install") ?>
            </a>
        <?php endif ?>
    </div>
</div>
