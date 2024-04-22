<?php if ($toolbar): ?>
    <?= $toolbar->render() ?>
<?php endif ?>

<?php if (!$useGroups): ?>
    <div class="list-widget-container">
        <?php if ($filter): ?>
            <?= $filter->render() ?>
        <?php endif ?>

        <?= $list->render() ?>
    </div>
<?php else: ?>
    <div class="ps-lg-4">
        <div class="list-with-sidebar">
            <div class="sidebar-area" id="<?= $this->getId('listTabs') ?>">
                <?= $this->makePartial('list_tabs') ?>
            </div>
            <div class="sidebar-list">
                <div class="layout-row">
                    <div class="list-widget-container">
                        <?php if ($filter): ?>
                            <?= $filter->render() ?>
                        <?php endif ?>

                        <?= $list->render() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
