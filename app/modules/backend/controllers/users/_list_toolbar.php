<div data-control="toolbar">
    <?php if (BackendAuth::userHasAccess('admins.manage.create')): ?>
        <a href="<?= Backend::url('backend/users/create') ?>" class="btn btn-primary oc-icon-plus">
            <?= e(trans('backend::lang.user.new')) ?>
        </a>
    <?php endif ?>
</div>
