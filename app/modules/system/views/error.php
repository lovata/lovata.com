<!DOCTYPE html>
<html lang="<?= App::getLocale() ?>">
    <head>
        <title><?= Lang::get('system::lang.page.custom_error.label') ?></title>
        <link href="<?= Url::asset('/modules/system/assets/css/styles.css') ?>" rel="stylesheet" />
        <script src="<?= Url::asset('modules/system/assets/js/framework-bundle.min.js') ?>"></script>
        <meta name="turbo-visit-control" content="disable" />
        <meta charset="utf-8" />
    </head>
    <body>
        <div class="container">
            <h1><i class="icon-power-off warning"></i> <?= Lang::get('system::lang.page.custom_error.label') ?></h1>
            <p class="lead"><?= Lang::get('system::lang.page.custom_error.help') ?></p>
        </div>
    </body>
</html>
