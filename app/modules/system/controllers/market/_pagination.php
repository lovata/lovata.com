<nav>
    <ul class="pagination">
        {{#pageCurrentGt1}}
            <li class="page-item">
                <a
                    href="<?= $pageUrl ?>=1"
                    class="page-link page-first"
                    data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                    title="<?= e(trans('backend::lang.list.first_page')) ?>">
                    <i class="icon-angle-double-left"></i>
                </a>
            </li>
        {{/pageCurrentGt1}}
        {{^pageCurrentGt1}}
            <li class="page-item">
                <span
                    class="page-link page-first"
                    title="<?= e(trans('backend::lang.list.first_page')) ?>">
                    <i class="icon-angle-double-left"></i>
                </span>
            </li>
        {{/pageCurrentGt1}}

        {{#pageCurrentGt1}}
            <li class="page-item">
                <a
                    href="<?= $pageUrl ?>={{pageCurrentMinus1}}"
                    class="page-link page-back"
                    data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                    title="<?= e(trans('backend::lang.list.prev_page')) ?>">
                    <i class="icon-angle-left"></i>
                </a>
            </li>
        {{/pageCurrentGt1}}
        {{^pageCurrentGt1}}
            <li class="page-item">
                <span
                    class="page-link page-back"
                    title="<?= e(trans('backend::lang.list.prev_page')) ?>">
                    <i class="icon-angle-left"></i>
                </span>
            </li>
        {{/pageCurrentGt1}}

        <li class="page-item active">
            <span class="page-link page-active">
                {{pageCurrent}}
            </span>
        </li>

        {{#hasMorePages}}
            <li class="page-item">
                <a
                    href="<?= $pageUrl ?>={{pageCurrentPlus1}}"
                    class="page-link page-next"
                    data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                    title="<?= e(trans('backend::lang.list.next_page')) ?>">
                    <i class="icon-angle-right"></i>
                </a>
            </li>
        {{/hasMorePages}}
        {{^hasMorePages}}
            <li class="page-item">
                <span
                    class="page-link page-next"
                    title="<?= e(trans('backend::lang.list.next_page')) ?>">
                    <i class="icon-angle-right"></i>
                </span>
            </li>
        {{/hasMorePages}}
    </ul>
</nav>
