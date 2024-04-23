<div
    class="toolbar-widget <?= $cssClasses ?>"
    id="<?= $this->getId() ?>"
    <?= $this->listWidgetId ? 'data-list-linkage="'.$this->listWidgetId.'"' : '' ?>
>
    <div class="control-toolbar">

        <!-- Control Panel -->
        <div class="toolbar-item toolbar-primary">
            <?= $controlPanel ?>
        </div>

        <!-- List Search -->
        <?php if ($search): ?>
            <div class="toolbar-item" data-calculate-width>
                <?= $search ?>
            </div>
        <?php endif ?>

        <!-- List Setup -->
        <?php if ($setupHandler): ?>
            <div class="toolbar-item toolbar-setup">
                <a href="javascript:;"
                    title="<?= __("List Setup") ?>"
                    data-control="popup"
                    data-handler="<?= $setupHandler ?>"><span></span></a>
            </div>
        <?php endif ?>

    </div>
</div>
