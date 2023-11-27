<div class="facet-item">
    <div class="input-with-icon size-sm right-align">
        <i class="icon icon-calendar-o"></i>
        <input
            type="text"
            name="Filter[afterRaw]"
            value="<?= e($scope->afterRaw) ?>"
            class="form-control form-control-sm popup-allow-focus w-120"
            autocomplete="off"
            data-datepicker
            data-datepicker-target="<?= $scope->getId('after') ?>" />
        <input
            type="hidden"
            name="Filter[after]"
            id="<?= $scope->getId('after') ?>"
            value="<?= e($scope->after) ?>"
            />
    </div>
</div>
<div class="facet-item">
    <span><?= __('and') ?></span>
</div>
<div class="facet-item">
    <div class="input-with-icon size-sm right-align">
        <i class="icon icon-calendar-o"></i>
        <input
            type="text"
            name="Filter[beforeRaw]"
            value="<?= e($scope->beforeRaw) ?>"
            class="form-control form-control-sm popup-allow-focus w-120"
            autocomplete="off"
            data-datepicker
            data-datepicker-target="<?= $scope->getId('before') ?>" />
        <input
            type="hidden"
            name="Filter[before]"
            id="<?= $scope->getId('before') ?>"
            value="<?= e($scope->before) ?>"
            />
    </div>
</div>
