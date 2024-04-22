<?php if (!$this->fatalError): ?>

<?= Form::open(['class'=>'layout settings-page size-large']) ?>
    <div class="layout-row">
        <div class="form-group hint-field span-full">
            <div class="callout fade show callout-info no-subheader ">
                <div class="header">
                    <h3><?= e(trans('cms::lang.internal_traffic_statistics.hint')) ?></h3>
                </div>
            </div>
        </div>

        <div class="form-group">
            <p>
                <?php if ($featureEnabled): ?>
                    <?= e(trans('cms::lang.internal_traffic_statistics.enabled')) ?>
                <?php else: ?>
                    <?= e(trans('cms::lang.internal_traffic_statistics.disabled')) ?>
                <?php endif ?>
            </p>
        </div>

        <?php if ($featureEnabled): ?>
            <table class="table">
                <tr>
                    <td><?= e(trans('cms::lang.internal_traffic_statistics.timezone')) ?></td>
                    <td><?= e($timezone) ?></td>
                </tr>
                <tr>
                    <td><?= e(trans('cms::lang.internal_traffic_statistics.retention')) ?></td>
                    <td><?= e($retention) ?></td>
                </tr>
            </table>
        <?php endif ?>
    </div>

    <div class="form-buttons">
        <div class="loading-indicator-container">
            <span class="btn-text">
                <a href="<?= Backend::url('system/settings') ?>"><?= e(trans('backend::lang.form.cancel')) ?></a>
            </span>

            <button
                type="button"
                class="btn btn-danger pull-right"
                data-request="onPurgeData"
                data-load-indicator="<?= e(trans('cms::lang.internal_traffic_statistics.purging')) ?>"
                data-request-confirm="<?= e(trans('cms::lang.internal_traffic_statistics.purge_data_confirm')) ?>">
                <?= e(trans('cms::lang.internal_traffic_statistics.purge_button')) ?>
            </button>
        </div>
    </div>
<?= Form::close() ?>

<?php else: ?>
    <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>
    <p><a href="<?= Backend::url('system/settings') ?>" class="btn btn-default"><?= __('Return to System Settings') ?></a></p>
<?php endif ?>