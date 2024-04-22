<?php
    $section = $this->activeSource;
    $showExport = $section->showExport ?? true;
    $showImport = ($section->showImport ?? true) && $this->hasSourcePermission('create');
?>
<div data-control="toolbar" data-list-linkage="<?= $this->listGetId() ?>">

    <?php if ($this->hasSourcePermission('create')): ?>
        <?= Ui::button($section->getMessage('buttonCreate', "New :name", ['name' => e(__($section->name))]), 'tailor/entries/'.$section->handleSlug.'/create')
            ->labelHtml()
            ->icon('icon-plus')
            ->primary() ?>

        <div class="toolbar-divider"></div>
    <?php endif ?>

    <?php if ($this->hasSourcePermission('publish', 'delete')): ?>
        <div id="listBulkActions" class="btn-container">
            <?= $this->makePartial('list_bulk_actions') ?>
        </div>
    <?php endif ?>

    <?php if ($showImport || $showExport): ?>
        <div class="dropdown dropdown-fixed">
            <?= Ui::button("More Actions")
                ->attributes(['data-toggle' => 'dropdown'])
                ->circleIcon('icon-ellipsis-v')
                ->secondary()
            ?>
            <ul class="dropdown-menu">
                <?php if ($showImport): ?>
                    <li>
                        <?= Ui::button("Import", 'tailor/bulkactions/'.$section->handleSlug.'/import')
                            ->replaceCssClass('dropdown-item')
                            ->icon('icon-upload') ?>
                    </li>
                <?php endif ?>
                <?php if ($showExport): ?>
                    <li>
                        <?= Ui::button("Export", 'tailor/bulkactions/'.$section->handleSlug.'/export')
                            ->replaceCssClass('dropdown-item')
                            ->icon('icon-download') ?>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    <?php endif ?>
</div>
