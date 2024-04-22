<div
    class="dashboard-report-widget-indicator"
    :class="{'loading': loading}"
    data-lang-prop-title="<?= e(trans('backend::lang.dashboard.widget_title')) ?>"
    data-lang-prop-show-interval="<?= e(trans('backend::lang.dashboard.section_show_interval')) ?>"
>
    <h3 class="dashboard-section" v-text="title"></h3>
</div>