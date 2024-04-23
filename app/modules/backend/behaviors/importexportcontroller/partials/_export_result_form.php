<?php if (!$this->fatalError): ?>

    <div class="modal-body">
        <p>
            <?= __("File export process completed!") ?>
            <?= __("The browser will now redirect to the file download.") ?>
        </p>
    </div>
    <div class="modal-footer">
        <a
            href="<?= $returnUrl ?>"
            class="btn btn-success"
            data-dismiss="popup">
            <?= __("Complete") ?>
        </a>
        <button
            type="button"
            class="btn btn-secondary"
            data-dismiss="popup">
            <?= __("Close") ?>
        </button>
    </div>

    <script> window.location = '<?= $fileUrl ?>'; </script>

<?php else: ?>

    <div class="modal-body">
        <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    </div>
    <div class="modal-footer">
        <button
            type="button"
            class="btn btn-secondary"
            data-dismiss="popup">
            <?= __("Close") ?>
        </button>
    </div>

<?php endif ?>
