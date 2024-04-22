<div
    class="dashboard-report-widget-chart"
    :class="{'loading': loading}"
    data-lang-prop-chart-type="<?= e(trans('backend::lang.dashboard.widget_chart_type')) ?>"
    data-lang-chart-type-bar="<?= e(trans('backend::lang.dashboard.widget_chart_type_bar')) ?>"
    data-lang-chart-type-stacked-bar="<?= e(trans('backend::lang.dashboard.widget_chart_type_stacked_bar')) ?>"
    data-lang-chart-type-line="<?= e(trans('backend::lang.dashboard.widget_chart_type_line')) ?>"
    data-lang-prop-bar-direction="<?= e(trans('backend::lang.dashboard.widget_bar_direction')) ?>"
    data-lang-bar-direction-vertical="<?= e(trans('backend::lang.dashboard.widget_bar_direction_vertical')) ?>"
    data-lang-bar-direction-horizontal="<?= e(trans('backend::lang.dashboard.widget_bar_direction_horizontal')) ?>"
>
    <div class="widget-body">
        <h3 class="widget-title" v-if="widget.configuration.title" v-text="widget.configuration.title"></h3>

        <div v-if="showMetricsTotalRow" class="totals">
            <div
                class="total-cell"
                v-for="metricData in configuration.metrics"
                v-if="metricData.display_totals"
            >
                <div v-if="loading" class="skeleton-container">
                    <div class="skeleton-name data-skeleton dashboard-widget-loading-pulse"></div>
                    <div class="skeleton-value data-skeleton dashboard-widget-loading-pulse"></div>
                </div>
                <div v-else>
                    <div class="total-name">
                        <div class="total-color" :style="{'background-color': metricData.color}"></div>
                        <span v-text="metricsData[metricData.metric].label"></span>
                    </div>
                    <div class="total-container">
                        <div class="dashboard-total-value" v-text="getMetricTotal(metricData.metric)"></div>
                        <backend-component-dashboard-report-diff
                            :prevValue="getMetricTotalClean(metricData.metric, true)"
                            :currentValue="getMetricTotalClean(metricData.metric)"
                            :formattingOptions="getMetricIntlFormatOptions(metricData.metric)"
                            :store="store"
                        >
                        </backend-component-dashboard-report-diff>
                    </div>
                </div>
            </div>
        </div>

        <div class="widget-chart-container" :class="{'error': error}">
            <canvas ref="canvas"></canvas>
        </div>

        <backend-component-dashboard-widget-error
            v-if="error"
            :store="store"
            @configure="$emit('configure')"
        ></backend-component-dashboard-widget-error>
    </div>
</div>