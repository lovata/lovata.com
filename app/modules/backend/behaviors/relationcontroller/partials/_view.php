<?php if ($relationViewFilterWidget): ?>
    <?= $relationViewFilterWidget->render() ?>
<?php endif ?>

<?php if ($relationViewMode === 'single'): ?>
    <?= $relationViewFormWidget->render() ?>
<?php else: ?>
    <?= $relationViewListWidget->render() ?>
<?php endif ?>
