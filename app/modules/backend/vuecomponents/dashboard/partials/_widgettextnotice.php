<div
    class="dashboard-report-widget-notice"
    :class="{'loading': loading}"
    data-lang-prop-title="<?= e(trans('backend::lang.dashboard.widget_title')) ?>"
    data-lang-prop-notice-text="<?= e(trans('backend::lang.dashboard.notice_text')) ?>"
>
    <div class="widget-body">
        <h3 v-text="widget.configuration.title"></h3>
        <p v-if="widget.configuration.notice" v-text="widget.configuration.notice"></p>
    </div>
</div>