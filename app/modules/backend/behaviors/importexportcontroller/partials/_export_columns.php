<div id="exportColumns">
    <?php if (!$exportCustomFormat): ?>
        <label class="form-label">
            <?= __("Columns") ?>
        </label>
        <div class="export-columns">
            <div class="control-simplelist with-checkboxes is-sortable" data-control="simplelist">
                <ul>
                    <?php foreach ($exportColumns as $key => $column): ?>
                        <li>
                            <span class="drag-handle" title="<?= __("Reorder") ?>">
                                <i class="octo-icon-list-reorder"></i>
                            </span>
                            <div class="form-check">
                                <input
                                    type="hidden"
                                    name="export_columns[]"
                                    value="<?= $key ?>" />
                                <input
                                    class="form-check-input"
                                    id="<?= $this->getId('exportCheckbox-'.$key) ?>"
                                    name="visible_columns[<?= $key ?>]"
                                    value="1"
                                    checked="checked"
                                    type="checkbox" />
                                <label
                                    class="form-check-label"
                                    for="<?= $this->getId('exportCheckbox-'.$key) ?>">
                                        <?= e(__($column)) ?>
                                </label>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <p>
            <?= __("A custom schema is used for this file format.") ?>
        </p>
    <?php endif ?>
</div>
