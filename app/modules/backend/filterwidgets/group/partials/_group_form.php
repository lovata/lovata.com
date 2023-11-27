<div
    data-control="groupfilter"
    data-options-handler="<?= $this->getEventHandler('onGetGroupOptions') ?>"
    data-group-template="#<?= $this->getId('groupTemplate') ?>"
>
    <input
        type="hidden"
        name="Filter[value]"
        value="<?= $scope->value ? e(json_encode($scope->value)) : '' ?>"
        data-groupfilter-datalocker />
    <div data-groupfilter-container></div>
</div>

<script type="text/template" id="<?= $this->getId('groupTemplate') ?>">
    <div class="filter-search search-input-container storm-icon-pseudo loading-indicator-container size-input-text">
        <input
            type="text"
            name="search"
            autocomplete="off"
            class="filter-search-input form-control popup-allow-focus"
            data-request="{{ optionsHandler }}"
            data-load-indicator-opaque
            data-load-indicator
            data-track-input />
        <div class="filter-items">
            <ul>
                {{#available}}
                    <li data-item-id="{{ id }}"><a href="javascript:;">{{ name }}</a></li>
                {{/available}}
                {{#loading}}
                    <li class="loading"><span></span></li>
                {{/loading}}
            </ul>
        </div>
        <div class="filter-active-items">
            <ul>
                {{#active}}
                    <li data-item-id="{{ id }}"><a href="javascript:;">{{ name }}</a></li>
                {{/active}}
            </ul>
        </div>
        <div class="filter-buttons">
            <button class="btn btn-sm btn-primary" data-filter-action="apply">
                <?= __("Apply") ?>
            </button>
            <div class="flex-grow-1"></div>
            <button class="btn btn-sm btn-secondary" data-filter-action="clear">
                <?= __("Clear") ?>
            </button>
        </div>
    </div>
</script>
