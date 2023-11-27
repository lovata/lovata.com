<?php foreach ($scopes as $scope): ?>
    <?= $this->makePartial('scope-container', ['scope' => $scope]) ?>
<?php endforeach ?>
