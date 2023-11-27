<div id="<?= $this->getId('popup') ?>" data-control="snippetlookup" class="snippetlookup-popup h-100">
    <?= Form::ajax($this->getEventHandler('onInsertSnippet'), [
        'data-popup-load-indicator' => true,
        'class' => 'd-flex flex-column h-100'
    ]) ?>
        <input type="hidden" name="snippetmanager_flag" value="1" />
        <div class="modal-header">
            <h4 class="modal-title"><?= e(__($title)) ?></h4>
            <button type="button" class="btn-close" data-dismiss="popup"></button>
        </div>
        <div>
            <?= $this->makePartial('toolbar') ?>
        </div>
        <div class="flex-grow-1 position-relative">
            <?= $this->makePartial('snippets', [
                'data' => $this->getData()
            ]) ?>
        </div>
    <?= Form::close() ?>
</div>
