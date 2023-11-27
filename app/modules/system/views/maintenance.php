<!DOCTYPE html>
<html lang="<?= App::getLocale() ?>">
    <head>
        <title><?= Lang::get('system::lang.page.maintenance.label') ?></title>
        <link href="<?= Url::asset('/modules/system/assets/css/styles.css') ?>" rel="stylesheet" />
        <script src="<?= Url::asset('modules/system/assets/js/framework-bundle.min.js') ?>"></script>
        <meta name="turbo-visit-control" content="disable" />
        <meta charset="utf-8" />
    </head>
    <body>
        <div class="container">
            <h1><i class="icon-wrench warning"></i> <?= Lang::get('system::lang.page.maintenance.label') ?></h1>
            <p class="lead"><?= Lang::get('system::lang.page.maintenance.help') ?></p>
            <?php if ($message): ?>
                <p class="lead">
                    <strong><?= Lang::get('system::lang.page.maintenance.message') ?></strong>
                    <?= e($message) ?>
                </p>
            <?php endif ?>
            <?php if ($retryAfter): ?>
                <p class="lead">
                    <strong><?= Lang::get('system::lang.page.maintenance.available_at') ?></strong>
                    <?= now()->addRealSeconds($retryAfter + 1)->diffForHumans() ?>
                </p>
            <?php endif ?>
        </div>
    </body>
</html>
