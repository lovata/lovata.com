
<?php
    $drafts = $primaryModel->getDraftRecords();
    $versions = $primaryModel->getVersionRecords();
?>
<?php if (count($drafts) || count($versions)): ?>
    <?= Ui::callout(function() use ($drafts, $versions, $activeSource, $primaryModel) { ?>
        <p>
            <a href="<?= Backend::url('tailor/entries/'.$activeSource->handle.'/'.$primaryModel->getKey()) ?>">
                Current Record
            </a>
        </p>

        <?php if (count($drafts)): ?>
            <h6 class="m-t-0">Drafts</h6>
            <ul>
                <?php foreach ($drafts as $draft): ?>
                    <li>
                        <div>
                            <a href="?draft=<?= $draft->getDraftId() ?>">
                                <?= e($draft->getDraftName()) ?> - by Admin Person
                            </a>
                            <span><?= $draft->updated_at ?></span>
                        </div>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

        <?php if (count($versions)): ?>
            <h6 class="m-t-0">Versions</h6>
            <ul>
                <?php foreach ($versions as $version): ?>
                    <li>
                        <div>
                            <a href="?version=<?= $version->getVersionId() ?>">
                                Version <?= e($version->build) ?> - by Admin Person
                            </a>
                            <span><?= $version->updated_at ?></span>
                        </div>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    <?php }) ?>
<?php endif ?>
