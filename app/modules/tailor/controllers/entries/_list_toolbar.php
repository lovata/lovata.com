<?php
    $section = $this->activeSource;
?>
<div data-control="toolbar" data-list-linkage="<?= $this->listGetId() ?>">

    <?php if ($this->hasSourcePermission('create')): ?>
        <a href="<?= Backend::url('tailor/entries/'.$section->handleSlug.'/create') ?>" class="btn btn-primary">
            <?= $section->getMessage('buttonCreate', "Create :name Entry", ['name' => "<strong>".e(__($section->name))."</strong>"]) ?>
        </a>
    <?php endif ?>

    <?php if ($this->hasSourcePermission('publish', 'delete')): ?>
        <div class="btn-group dropdown dropdown-fixed" id="listBulkActions">
            <?= $this->makePartial('list_bulk_actions') ?>
        </div>
    <?php endif ?>

    <?php if ($section->showExport ?? true): ?>
        <a
            href="<?= Backend::url('tailor/bulkactions/'.$section->handleSlug.'/export') ?>"
            class="btn btn-secondary oc-icon-download">
            <?= __("Export") ?>
        </a>
    <?php endif ?>

    <?php if (($section->showImport ?? true) && $this->hasSourcePermission('create')): ?>
        <a
            href="<?= Backend::url('tailor/bulkactions/'.$section->handleSlug.'/import') ?>"
            class="btn btn-secondary oc-icon-upload">
            <?= __("Import") ?>
        </a>
    <?php endif ?>
</div>
