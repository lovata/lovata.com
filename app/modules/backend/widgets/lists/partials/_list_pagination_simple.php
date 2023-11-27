<?php
    $transportMethod = $pageName === '_page' ? 'data' : 'query';
?>
<div class="list-pagination">
    <nav class="list-pagination-links ms-auto loading-indicator-container size-small">
        <ul class="pagination">
            <?php if ($pageCurrent > 1): ?>
                <li class="page-item">
                    <a
                        href="javascript:;"
                        class="page-link page-first"
                        data-request="<?= $this->getEventHandler('onPaginate') ?>"
                        data-request-<?= $transportMethod ?>="{ <?= e($pageName) ?>: 1 }"
                        data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                        title="<?= e(trans('backend::lang.list.first_page')) ?>">
                        <i class="icon-angle-double-left"></i>
                    </a>
                </li>
            <?php else: ?>
            <li class="page-item disabled">
                <span
                    class="page-link page-first"
                    title="<?= e(trans('backend::lang.list.first_page')) ?>">
                    <i class="icon-angle-double-left"></i>
                </span>
            <?php endif ?>
            <?php if ($pageCurrent > 1): ?>
                <li class="page-item">
                    <a
                        href="javascript:;"
                        class="page-link page-back"
                        data-request="<?= $this->getEventHandler('onPaginate') ?>"
                        data-request-<?= $transportMethod ?>="{ <?= e($pageName) ?>: <?= $pageCurrent - 1 ?> }"
                        data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                        title="<?= e(trans('backend::lang.list.prev_page')) ?>">
                        <i class="icon-angle-left"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span
                        class="page-link page-back"
                        title="<?= e(trans('backend::lang.list.prev_page')) ?>">
                        <i class="icon-angle-left"></i>
                    </span>
                </li>
            <?php endif ?>
            <li class="page-item active">
                <span class="page-link page-active">
                    <?= $pageCurrent ?>
                </span>
            </li>
            <?php if ($hasMorePages): ?>
                <li class="page-item">
                    <a
                        href="javascript:;"
                        class="page-link page-next"
                        data-request-<?= $transportMethod ?>="{ <?= e($pageName) ?>: <?= $pageCurrent + 1 ?> }"
                        data-request="<?= $this->getEventHandler('onPaginate') ?>"
                        data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                        title="<?= e(trans('backend::lang.list.next_page')) ?>">
                        <i class="icon-angle-right"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span
                        class="page-link page-next"
                        title="<?= e(trans('backend::lang.list.next_page')) ?>">
                        <i class="icon-angle-right"></i>
                    </span>
                </li>
            <?php endif ?>
        </ul>
    </nav>
</div>
