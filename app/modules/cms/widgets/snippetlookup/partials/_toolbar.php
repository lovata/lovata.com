<div class="relative toolbar-item loading-indicator-container size-input-text">
    <?= Ui::searchInput(__("Search..."), 'search')
        ->value($this->getSearchTerm())
        ->ajaxHandler($this->getEventHandler('onSearch'))
        ->isModal() ?>
</div>
