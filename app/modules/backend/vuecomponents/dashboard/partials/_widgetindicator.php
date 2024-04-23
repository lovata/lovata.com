<div
    class="dashboard-report-widget-indicator"
    :class="{'loading': explicitLoading}"
    data-lang-prop-metric-value="<?= e(trans('backend::lang.dashboard.widget_metric_value')) ?>"
    data-lang-metrics="<?= e(trans('backend::lang.dashboard.widget_metrics')) ?>"
    data-lang-prop-icon-status="<?= e(trans('backend::lang.dashboard.widget_icon_status')) ?>"
    data-lang-prop-href="<?= e(trans('backend::lang.dashboard.widget_href')) ?>"
    data-lang-prop-icon="<?= e(trans('backend::lang.dashboard.widget_icon')) ?>"
    data-lang-prop-icon-required="<?= e(trans('backend::lang.dashboard.widget_icon_required')) ?>"
    data-lang-prop-link-text="<?= e(trans('backend::lang.dashboard.widget_link_text')) ?>"
    data-lang-icon-status-info="<?= e(trans('backend::lang.dashboard.icon_status_info')) ?>"
    data-lang-icon-status-important="<?= e(trans('backend::lang.dashboard.icon_status_important')) ?>"
    data-lang-icon-status-success="<?= e(trans('backend::lang.dashboard.icon_status_success')) ?>"
    data-lang-icon-status-warning="<?= e(trans('backend::lang.dashboard.icon_status_warning')) ?>"
    data-lang-icon-status-disabled="<?= e(trans('backend::lang.dashboard.icon_status_disabled')) ?>"
>
    <div class="indicator-body" :class="{'dashboard-widget-loading-pulse': explicitLoading && isConfigured}">
        <backend-component-dashboard-widget-error
            v-if="error"
            :store="store"
            @configure="$emit('configure')"
        ></backend-component-dashboard-widget-error>

        <template v-if="!error">
            <div class="indicator-icon" :class="iconStatusClass">
                <i :class="widget.configuration.icon"></i>
                <span v-if="complicationClass" class="icon-complication" :class="complicationClass"></span>
            </div>
            <div class="indicator-details">
                <h3 class="widget-title">
                    <span v-if="explicitLoading">&nbsp;</span>
                    <span v-else v-text="widget.configuration.title"></span>
                </h3>
                <p :class="{'total-container align-left': !explicitLoading}">
                    <span v-if="explicitLoading">&nbsp;</span>
                    <template v-else>
                        <span v-text="valueText"></span>
                        <template v-if="prevPeriodDiff !== null">
                            <span
                                title="<?= e(trans('backend::lang.dashboard.previous_period')) ?>"
                                class="prev-period-marker"
                                :class="{'negative': prevPeriodDiff < 0, 'neutral': prevPeriodDiff === 0}"
                            >
                                <i class="ph ph-arrow-up" v-if="prevPeriodDiff > 0"></i>
                                <i class="ph ph-arrow-down" v-if="prevPeriodDiff < 0"></i>
                                <span
                                    v-text="prevPeriodDiffFormattedAbs"
                                    v-bind:aria-label="prevPeriodDiffFormatted"
                                ></span>
                            </span>
                        </template>
                    </template>
                </p>
            </div>
        </template>
    </div>
    <template v-if="widget.configuration.link_text && !error">
        <div class="indicator-link-container" v-if="linkEnabled">
            <a
                class="indicator-link"
                draggable="false"
                v-if="!loadingPopupData"
                :href="linkHrefProcessed"
                @click="onLinkClick"
            >
                <span v-if="explicitLoading" class="dashboard-widget-loading-pulse">&nbsp;</span>
                <span v-else v-text="widget.configuration.link_text"></span>
            </a>
            <backend-component-loading-indicator v-else
                size="tiny"
            ></backend-component-loading-indicator>
        </div>
        <div
            class="indicator-link-container disabled"
            v-else
        >
            <span v-if="explicitLoading" class="dashboard-widget-loading-pulse">&nbsp;</span>
            <span v-else v-text="widget.configuration.link_text"></span>
        </div>
    </template>
</div>