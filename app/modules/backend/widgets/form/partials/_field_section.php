<!-- Section -->
<div class="field-section">
    <?php if ($field->label): ?>
        <h4><?= e(__($field->label)) ?></h4>
    <?php endif ?>

    <?php if ($field->comment): ?>
        <p class="form-text"><?= $field->commentHtml ? trans($field->comment) : e(__($field->comment)) ?></p>
    <?php endif ?>
</div>
