<div class="facet-item">
    <div class="input-with-icon size-sm right-align w-120">
        <i class="icon icon-calendar-o"></i>
        <input
            type="text"
            name="Filter[valueRaw]"
            value="<?= e($scope->valueRaw) ?>"
            class="form-control form-control-sm popup-allow-focus"
            autocomplete="off"
            data-datepicker
            data-datepicker-target="<?= $scope->getId('value') ?>" />
        <input
            type="hidden"
            name="Filter[value]"
            id="<?= $scope->getId('value') ?>"
            value="<?= e($scope->value) ?>"
        />
    </div>
</div>
