<?php
    $installHandler = $product->isTheme
        ? $this->updaterWidget->getEventHandler('onInstallTheme')
        : $this->updaterWidget->getEventHandler('onInstallPlugin');

    $removeHandler = $product->isTheme
        ? $this->updaterWidget->getEventHandler('onRemoveTheme')
        : $this->updaterWidget->getEventHandler('onRemovePlugin');
?>
<div class="card mb-1 ms-auto" style="width:10.5rem">
    <div class="card-body text-center">
        <?php if ($projectDetails): ?>
            <?php if (!$product->installed()): ?>
                <?php if ($product->canInstall): ?>
                    <a
                        href="javascript:;"
                        data-control="popup"
                        data-handler="<?= $installHandler ?>"
                        data-request-data="code: '<?= e($product->code) ?>'"
                        class="btn btn-success oc-icon-plus">
                        <?= __("Install") ?>
                    </a>
                <?php else: ?>
                    <a
                        href="<?= e($product->homepage) ?>"
                        target="_blank"
                        rel="nofollow"
                        class="btn btn-success oc-icon-external-link">
                        <?= __("Buy Now") ?>
                    </a>
                <?php endif ?>
            <?php else: ?>
                <a
                    href="javascript:;"
                    data-control="popup"
                    data-handler="<?= $removeHandler ?>"
                    data-request-data="code: '<?= e($product->code) ?>'"
                    class="btn btn-danger oc-icon-chain-broken">
                    <?= __("Remove") ?>
                </a>
            <?php endif ?>
        <?php else: ?>
            <a
                href="javascript:;"
                class="btn btn-success oc-icon-plus disabled">
                <?= __("Install") ?>
            </a>
        <?php endif ?>
    </div>
</div>
