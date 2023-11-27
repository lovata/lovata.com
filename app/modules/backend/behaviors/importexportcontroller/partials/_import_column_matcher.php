<div id="importColumnMatcher">
    <?php if (!$importCustomFormat): ?>
        <div class="form-group">
            <?= $this->importExportMakePartial('import_toolbar') ?>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">
                        <?= __("File columns") ?>
                    </label>
                    <?= $this->importExportMakePartial('import_file_columns') ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">
                        <?= __("Database fields") ?>
                    </label>
                    <?= $this->importExportMakePartial('import_db_columns') ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p>
            <?= __("A custom schema is used for this file format.") ?>
        </p>
    <?php endif ?>
</div>
