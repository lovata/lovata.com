<?php
    $grouped = $sites->groupBy('group.name');
?>
<?php foreach ($grouped as $groupName => $groupSites): ?>
    <li class="mainmenu-item section-title">
        <span class="nav-label"><?= $groupName ?></span>
    </li>
    <?= $this->makePartial('submenu_items', ['sites' => $groupSites]) ?>
<?php endforeach ?>
