<div
    id="<?= $this->getId() ?>"
    class="control-filter"
    data-control="filterwidget"
    data-popover-template="#<?= $this->getId('popoverTemplate') ?>"
    data-update-handler="<?= $this->getEventHandler('onFilterUpdate') ?>"
    data-load-handler="<?= $this->getEventHandler('onLoadFilterForm') ?>"
>
    <?= $this->makePartial('filter-container') ?>
</div>

<!-- Popover Template -->
<?= $this->makePartial('popover_template') ?>
