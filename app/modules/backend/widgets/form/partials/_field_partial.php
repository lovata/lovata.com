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
<?php if (str_contains($field->path, '::')): ?>
    <?= View::make($field->path, $vars) ?>
<?php elseif ($field->path && File::isPathSymbol($field->path)): ?>
    <?= $this->controller->makePartial($field->path, $vars) ?>
<?php else: ?>
    <?php
        // @deprecated forever?
        $oldViewFile = $field->path ?: ltrim($field->fieldName, '_');
        $viewFile = $field->path ?: 'field_' . ltrim($field->fieldName, '_');
        $modelPath = $this->guessViewPathFrom($formModel);
    ?>
    <?= ($this->controller->makePartial("{$modelPath}/{$viewFile}", $vars, false)
            ?: $this->controller->makePartial($viewFile, $vars, false))
            ?: $this->controller->makePartial($oldViewFile, $vars) ?>
<?php endif ?>
