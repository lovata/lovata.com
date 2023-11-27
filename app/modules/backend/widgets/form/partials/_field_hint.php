<?php if ($field->path): ?>
    <?= $this->controller->makeHintPartial($field->getId(), $field->path, [
        'formModel' => $formModel,
        'formField' => $field,
        'formValue' => $field->value,
        'model' => $formModel,
        'field' => $field,
        'value' => $field->value
    ]) ?>
<?php else: ?>
    <?php
        $callout = Ui::callout();

        if ($field->label) {
            $callout->label(e(__($field->label)));
        }

        if ($field->comment) {
            $callout->commentHtml(
                $field->commentHtml
                    ? '<p>'.trans($field->comment).'</p>'
                    : Markdown::parse(e(__($field->comment)))
            );
        }

        switch ($field->getConfig('mode')) {
            case 'success':
                $callout->success();
                break;
            case 'danger':
                $callout->danger();
                break;
            case 'warning':
                $callout->warning();
                break;
            case 'tip':
            case 'info':
            default:
                $callout->tip();
                break;
        }
    ?>
    <?= $callout ?>
<?php endif ?>
