<?php if ($value): ?>
    <ul class="list-link-list">
        <?php
            $url = Backend::url('tailor/entries/'.$value->blueprint->handleSlug.'/'.$value->id);
        ?>
        <li><a href="<?= $url ?>"><?= e($value->title) ?></a></li>
    </ul>
<?php endif ?>
