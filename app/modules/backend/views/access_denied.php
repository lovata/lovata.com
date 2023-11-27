<!DOCTYPE html>
<html lang="<?= App::getLocale() ?>">
    <head>
        <title><?= Lang::get('backend::lang.page.access_denied.label') ?></title>
        <link href="<?= Url::asset('/modules/system/assets/css/styles.css') ?>" rel="stylesheet" />
        <script src="<?= Url::asset('modules/system/assets/js/framework-bundle.min.js') ?>"></script>
        <meta name="turbo-visit-control" content="disable" />
        <meta charset="utf-8" />
    </head>
    <body>
        <div class="container">
            <h1><i class="icon-lock warning"></i> <?= Lang::get('backend::lang.page.access_denied.label') ?></h1>
            <p class="lead"><?= Lang::get('backend::lang.page.access_denied.help') ?></p>
            <p><a href="javascript:history.go(-1)"><?= Lang::get('backend::lang.page.not_found.back_link') ?></a></p>
            <?php if (BackendAuth::userHasAccess('general.backend')): ?>
                <p><a href="<?= Backend::url('') ?>"><?= Lang::get('backend::lang.page.access_denied.cms_link') ?></a></p>
            <?php endif ?>
        </div>
    </body>
</html>
