<h2><?= __("Getting Ready") ?></h2>

<div class="progress bar-loading-indicator" id="executeLoadingBar">
    <div class="progress-bar"></div>
</div>

<div class="loading-indicator-container">
    <div data-wait-longer="<?= __("Just a few more minutes") ?>..." class="mt-2 text-center"><?= __("Migrating Database") ?>...</div>
</div>

<script>
    jQuery(function() {
        var waitLonger = setTimeout(function() {
            $('[data-wait-longer]').text($('[data-wait-longer]').data('wait-longer'));
            $('#executeLoadingBar').addClass('fadeCycle');
        }, 80 * 1000);

        $.request('onMigrate').done(function(data) {
            clearTimeout(waitLonger);
        });
    });
</script>
