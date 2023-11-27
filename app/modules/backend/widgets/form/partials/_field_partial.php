<?php
    $vars = [
        'formModel' => $formModel,
        'formField' => $field,
        'formValue' => $field->value,
        'model' => $formModel,
        'field' => $field,
        'value' => $field->value
    ];
?>
<?php if (strpos($field->path, '::') !== false): ?>
    <?= View::make($field->path, $vars) ?>
<?php else: ?>
    <?= $this->controller->makePartial($field->path ?: ltrim($field->fieldName, '_'), $vars) ?>
<?php endif ?>
