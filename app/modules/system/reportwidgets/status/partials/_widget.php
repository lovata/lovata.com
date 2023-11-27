<div class="report-widget">
    <h3><?= e(__($this->property('title'))) ?></h3>

    <?php if (!isset($error)): ?>
        <div class="control-status-list">
            <ul>
                <li>
                    <?php if ($updates): ?>
                        <span class="status-icon warning"><i class="icon-exclamation"></i></span>
                        <span class="status-text warning">
                            <?= e(trans('backend::lang.dashboard.status.updates_pending')) ?>
                        </span>
                        <?php if ($canUpdate): ?>
                            <a
                                href="<?= Backend::url('system/updates') ?>"
                                class="status-label btn btn-sm btn-outline-primary"><?= e(trans('backend::lang.dashboard.status.updates_link')) ?></a>
                        <?php endif ?>
                    <?php else: ?>
                        <span class="status-icon success"><i class="icon-check"></i></span>
                        <span class="status-text success">
                            <?= e(trans('backend::lang.dashboard.status.updates_nil')) ?>
                        </span>
                    <?php endif ?>
                </li>
                <li>
                    <?php if ($warnings): ?>
                        <span class="status-icon warning"><i class="icon-exclamation"></i></span>
                        <span class="status-text warning">
                            <?= e(trans('backend::lang.dashboard.status.warnings_pending')) ?>
                        </span>
                        <a
                            href="javascript:;"
                            data-control="popup"
                            data-size="large"
                            data-handler="<?= $this->getEventHandler('onLoadWarningsForm') ?>"
                            class="status-label btn btn-sm btn-outline-warning"><?= e(trans('backend::lang.dashboard.status.warnings_link')) ?></a>
                    <?php else: ?>
                        <span class="status-icon success"><i class="icon-check"></i></span>
                        <span class="status-text success">
                            <?= e(trans('backend::lang.dashboard.status.warnings_nil')) ?>
                        </span>
                    <?php endif ?>
                </li>
                <?php if ($coreBuild): ?>
                    <li>
                        <span class="status-icon"><i class="icon-info"></i></span>
                        <span class="status-text">
                            <?= e(trans('backend::lang.dashboard.status.core_build')) ?>
                        </span>
                        <a
                            class="status-label btn btn-sm btn-outline-secondary"
                            href="<?= Backend::url('system/updates') ?>">v<?= $coreBuild ?></a>
                    </li>
                <?php endif ?>
                <li>
                    <span class="status-icon"><i class="icon-exclamation-triangle"></i></span>
                    <span class="status-text">
                        <?= e(trans('backend::lang.dashboard.status.event_log')) ?>
                        <?php if ($eventLogMsg): ?>&nbsp;<a href="<?= Backend::url('system/settings/update/october/system/log_settings') ?>"><i class="icon-exclamation-triangle text-warning" title="<?= e(__('Disabled')) ?>" data-toggle="tooltip" data-placement="right"></i></a><?php endif ?>
                    </span>
                    <a
                        class="status-label btn btn-sm btn-outline-secondary"
                        href="<?= Backend::url('system/eventlogs') ?>"><?= $eventLog ?></a>
                </li>
                <li>
                    <span class="status-icon"><i class="icon-file-o"></i></span>
                    <span class="status-text">
                        <?= e(trans('backend::lang.dashboard.status.request_log')) ?>
                        <?php if ($requestLogMsg): ?>&nbsp;<a href="<?= Backend::url('system/settings/update/october/system/log_settings') ?>"><i class="icon-exclamation-triangle text-warning" title="<?= e(__('Disabled')) ?>" data-toggle="tooltip" data-placement="right"></i></a><?php endif ?>
                    </span>
                    <a
                        class="status-label btn btn-sm btn-outline-secondary"
                        href="<?= Backend::url('system/requestlogs') ?>"><?= $requestLog ?></a>
                </li>
                <?php if ($appBirthday): ?>
                    <li>
                        <span class="status-icon"><i class="icon-calendar"></i></span>
                        <span class="status-text">
                            <?= e(trans('backend::lang.dashboard.status.app_birthday')) ?>
                        </span>
                        <span class="status-label link"><?= Backend::dateTime($appBirthday, ['formatAlias' => 'dateLong']) ?></span>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="callout callout-warning">
            <div class="content"><?= e($error) ?></div>
        </div>
    <?php endif ?>
</div>
