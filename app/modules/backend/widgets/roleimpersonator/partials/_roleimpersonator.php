<div class="control-roleimpersonator row gx-0 bg-secondary">
    <div class="col-sm-3">
        <div class="roleimpersonator-left">
            <button
                class="btn btn-sm btn-outline-light"
                data-request="<?= $this->getEventHandler('onStopImpersonateRole') ?>"
                data-request-data="redirect:1"
                data-stripe-load-indicator
            >
                <i class="icon-arrow-left"></i>
                <?= __("Return to Role Settings") ?>
            </button>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="roleimpersonator-contents">
            <?= __("You are viewing as") ?>
            <strong><?= e($this->getImpersonatingRole()->name) ?></strong>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="roleimpersonator-right">
            <button
                class="btn btn-sm btn-outline-light"
                data-request="<?= $this->getEventHandler('onStopImpersonateRole') ?>"
                data-stripe-load-indicator
            >
                <?= __("Cancel") ?>
            </button>
        </div>
    </div>
</div>
