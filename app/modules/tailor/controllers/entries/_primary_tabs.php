<div class="layout primary-tabs-container">
    <?= $this->formRender([
        'section' => 'primary',
        'preview' => $initialState['isDeleted']
    ]) ?>
    <input type="hidden" name="EntryRecord[content_group]" value="<?= e($formModel->content_group) ?>"/>
</div>
