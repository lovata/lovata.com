<?php foreach (Flash::all() as $type => $message): ?>
    <div data-control="flash-message" class="oc-hide" data-type="<?= $type ?>" data-interval="5"><?= e($message) ?></div>
<?php endforeach ?>
