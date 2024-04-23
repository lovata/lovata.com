<?php
$showHint = true;

if (System\Classes\PluginManager::instance()->hasPlugin('October.Drivers')) {
    $showHint = false;
}
else {
    $composer = \October\Rain\Composer\Manager::instance();
    if (
        $composer->hasPackage('aws-sdk-php') ||
        $composer->hasPackage('symfony/mailgun-mailer') ||
        $composer->hasPackage('symfony/postmark-mailer')
    ) {
        $showHint = false;
    }
}
?>
<?php if ($showHint): ?>
    <div class="callout callout-warning show in no-subheader">
        <div class="header">
            <i class="icon-exclamation-circle"></i>
            <h3><?= __("Drivers Not Installed") ?></h3>
        </div>
        <div class="content">
            <p><?= e(__("This mail method requires the plugin ':plugin' be installed before you can send mail.", ['plugin' => 'October.Drivers'])) ?></p>
        </div>
    </div>
<?php endif ?>
