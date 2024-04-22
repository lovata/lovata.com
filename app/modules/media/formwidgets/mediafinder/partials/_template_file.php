<!-- Template for new file -->
<script type="text/template" id="<?= $this->getId('template') ?>">
    <div class="item-object item-object-file <?= isset($modeMulti) ? 'mode-multi' : '' ?>">
        <?php if (isset($modeMulti)): ?>
            <div class="form-check">
                <input
                    class="form-check-input"
                    data-record-selector
                    type="checkbox"
                    value=""
                />
            </div>
            <a href="javascript:;" class="drag-handle"><i class="icon-list-reorder"></i></a>
        <?php endif ?>

        <div class="file-data-container">
            <div class="file-data-container-inner">
                <div class="icon-container">
                    <?php if (isset($modeFolder)): ?>
                        <i class="icon-folder"></i>
                    <?php else: ?>
                        <i class="icon-attachment"></i>
                    <?php endif ?>
                </div>

                <div class="info">
                    <h4 class="filename">
                        <span data-title></span>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</script>
