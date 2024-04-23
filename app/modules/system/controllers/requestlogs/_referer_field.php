<?php if ($formModel->referer && count($formModel->referer) > 0): ?>
    <div class="form-control control-simplelist with-icons">
        <ul>
            <?php foreach ((array) $formModel->referer as $referer): ?>
                <li class="oc-icon-file"><?= e($referer) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php else: ?>
    <div class="form-control"><em><?= __("There were no detected referrers to this URL.") ?></em></div>
<?php endif ?>
