<div class="control-scrollbar position-absolute top-0 start-0 end-0 bottom-0" data-control="scrollbar">
    <div
        class="control-filelist filelist-hero snippet-list"
        id="<?= $this->getId('snippet-list') ?>"
        data-control="filelist">
        <?= $this->makePartial('items', ['items' => $data]) ?>
    </div>
</div>
