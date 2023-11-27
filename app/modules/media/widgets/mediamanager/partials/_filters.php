<h3 class="section"><?= e(trans('backend::lang.media.display')) ?></h3>

<ul class="nav flex-column selector-group">
    <li role="presentation" class="nav-item <?= $currentFilter === Media\Widgets\MediaManager::FILTER_EVERYTHING ? 'active' : '' ?>">
        <a href="#" class="nav-link" data-command="set-filter" data-filter="<?= Media\Widgets\MediaManager::FILTER_EVERYTHING ?>">
            <i class="icon-recycle"></i>

            <?= e(trans('backend::lang.media.filter_everything')) ?>
        </a>
    </li>
    <li role="presentation" class="nav-item <?= $currentFilter === Media\Classes\MediaLibraryItem::FILE_TYPE_IMAGE ? 'active' : '' ?>">
        <a href="#" class="nav-link" data-command="set-filter" data-filter="<?= Media\Classes\MediaLibraryItem::FILE_TYPE_IMAGE ?>">
            <i class="icon-picture-o"></i>

            <?= e(trans('backend::lang.media.filter_images')) ?>
        </a>
    </li>
    <li role="presentation" class="nav-item <?= $currentFilter === Media\Classes\MediaLibraryItem::FILE_TYPE_VIDEO ? 'active' : '' ?>">
        <a href="#" class="nav-link" data-command="set-filter" data-filter="<?= Media\Classes\MediaLibraryItem::FILE_TYPE_VIDEO ?>">
            <i class="icon-video-camera"></i>

            <?= e(trans('backend::lang.media.filter_video')) ?>
        </a>
    </li>
    <li role="presentation" class="nav-item <?= $currentFilter === Media\Classes\MediaLibraryItem::FILE_TYPE_AUDIO ? 'active' : '' ?>">
        <a href="#" class="nav-link" data-command="set-filter" data-filter="<?= Media\Classes\MediaLibraryItem::FILE_TYPE_AUDIO ?>">
            <i class="icon-volume-up"></i>

            <?= e(trans('backend::lang.media.filter_audio')) ?>
        </a>
    </li>
    <li role="presentation" class="nav-item <?= $currentFilter === Media\Classes\MediaLibraryItem::FILE_TYPE_DOCUMENT ? 'active' : '' ?>">
        <a href="#" class="nav-link" data-command="set-filter" data-filter="<?= Media\Classes\MediaLibraryItem::FILE_TYPE_DOCUMENT ?>">
            <i class="icon-file"></i>

            <?= e(trans('backend::lang.media.filter_documents')) ?>
        </a>
    </li>
</ul>
