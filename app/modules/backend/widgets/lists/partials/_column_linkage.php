<?php if ($value): ?>
    <ul class="list-link-list">
        <li><a href="<?= e($linkUrl) ?>" <?= Html::attributes($attributes) ?>><?= e(__($linkText)) ?></a></li>
    </ul>
<?php endif ?>
