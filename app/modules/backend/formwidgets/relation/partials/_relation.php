<div class="relation-widget" id="<?= $this->getId() ?>">
    <?php if ($this->useController): ?>
        <?= $this->controller->relationRender($this->getRelationControllerFieldName(), [
            'readOnlyDefault' => $this->previewMode
        ]) ?>
    <?php else: ?>
        <?= $this->makePartial('~/modules/backend/widgets/form/partials/_field_'.$field->type.'.php', [
            'field' => $field
        ]) ?>
    <?php endif ?>
</div>
