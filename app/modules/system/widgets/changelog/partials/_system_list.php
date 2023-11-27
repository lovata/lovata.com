<div class="modal-header">
    <h4 class="modal-title"><?= e(trans('system::lang.updates.changelog')) ?></h4>
    <button type="button" class="btn-close" data-dismiss="popup"></button>
</div>
<div class="modal-body">

    <?php if ($this->fatalError): ?>
        <p class="flash-message static error"><?= e($fatalError) ?></p>
    <?php else: ?>
        <div class="control-updatelist">
            <div class="control-scrollbar" style="height:400px" data-control="scrollbar">
                <div class="update-item">
                    <dl>
                        <?php foreach ($changelog as $item): ?>
                            <?php
                                $description = array_get($item, 'description');
                                $versionFull = array_get($item, 'version_full');
                                $linkUrl = array_get($item, 'link_url');
                            ?>
                            <dt>v<?= e($versionFull) ?></dt>
                            <?php if ($linkUrl): ?>
                                <dd>
                                    <?= e($description) ?>
                                    <a href="<?= $linkUrl ?>" target="_blank">
                                        <?= e(trans('system::lang.updates.changelog_view_details')) ?>
                                        <i class="icon-external-link"></i>
                                    </a>
                                </dd>
                            <?php else: ?>
                                <dd><?= Markdown::parse(e($description)) ?></dd>
                            <?php endif ?>
                        <?php endforeach ?>
                    </dl>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<div class="modal-footer">
    <button
        type="button"
        class="btn btn-default"
        data-dismiss="popup">
        <?= e(trans('backend::lang.form.close')) ?>
    </button>
</div>
