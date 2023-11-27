<?php if ($value): ?>
    <ul class="list-link-list">
        <?php foreach ($value as $entry): ?>
            <?php
                $url = Backend::url('tailor/entries/'.$entry->blueprint->handleSlug.'/'.$entry->id);
            ?>
            <li><a href="<?= $url ?>"><?= e($entry->title) ?></a></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
