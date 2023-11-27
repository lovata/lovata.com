<!DOCTYPE html>
<html lang="<?= App::getLocale() ?>">
    <head>
        <title><?= Lang::get('system::lang.page.not_found.label') ?></title>
        <link href="<?= Url::asset('/modules/system/assets/css/styles.css') ?>" rel="stylesheet" />
        <script src="<?= Url::asset('modules/system/assets/js/framework-bundle.min.js') ?>"></script>
        <meta name="turbo-visit-control" content="disable" />
        <meta charset="utf-8" />
    </head>
    <body>
        <div class="container">
            <h1><i class="icon-chain-broken warning"></i> <?= Lang::get('system::lang.page.not_found.label') ?></h1>
            <p class="lead"><?= Lang::get('system::lang.page.not_found.help') ?></p>
        </div>
    </body>
</html>
