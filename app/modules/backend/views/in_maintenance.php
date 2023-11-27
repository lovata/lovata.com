<!DOCTYPE html>
<html lang="<?= App::getLocale() ?>">
    <head>
        <title><?= __("In Maintenance") ?></title>
        <link href="<?= Url::asset('/modules/system/assets/css/styles.css') ?>" rel="stylesheet" />
        <script src="<?= Url::asset('modules/system/assets/js/framework-bundle.min.js') ?>"></script>
        <meta name="turbo-visit-control" content="disable" />
        <meta charset="utf-8" />
    </head>
    <body>
        <div class="container">
            <h1><i class="icon-cogs warning"></i> <?= __("In Maintenance") ?></h1>
            <p class="lead"><?= __("The site is currently in maintenance mode, please check back later.") ?></p>
            <p><a href="javascript:history.go(-1)"><?= Lang::get('backend::lang.page.not_found.back_link') ?></a></p>
        </div>
    </body>
</html>
