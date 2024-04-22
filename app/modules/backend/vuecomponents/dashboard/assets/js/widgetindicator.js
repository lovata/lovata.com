oc.Modules.register('backend.component.dashboard.widget.indicator', function () {
    const phIconList = oc.Modules.import('backend.phosphor-icon-list');

    Vue.component('backend-component-dashboard-widget-indicator', {
        extends: oc.Modules.import('backend.vuecomponents.dashboard.widget-base'),
        data: function () {
            return {
                loadingPopupData: false
            }
        },
        computed: {
            isIndicatorDimension: function () {
                return typeof this.widget.configuration.dimension === 'string' &&
                    this.widget.configuration.dimension.startsWith('indicator@');
            },

            valueMetric: function () {
                return this.widget.configuration.metric ?? 'value';
            },

            iconStatusClass: function () {
                if (this.isIndicatorDimension) {
                    return this.getWidgetDataForMetric('icon_status', 'status-success', 'status-');
                }

                return this.configuration.icon_status;
            },

            linkEnabled: function () {
                if (this.store.state.editMode) {
                    return false;
                }

                if (this.isIndicatorDimension) {
                    return this.getWidgetDataForMetric('link_enabled', false);
                }

                return true;
            },

            valueText: function () {
                const result = this.getWidgetDataForMetric(this.valueMetric, '');
                if (this.isIndicatorDimension) {
                    return result;
                }

                return this.getMetricTotal(this.valueMetric);
            },

            prevPeriodDiff: function () {
                if (this.isIndicatorDimension) {
                    return null;
                }

                const currentTotal = this.getMetricTotalClean(this.valueMetric);
                const prevTotal = this.getMetricTotalClean(this.valueMetric, true);
                if (prevTotal === null) {
                    return null;
                }

                return currentTotal - prevTotal;
            },

            prevPeriodDiffFormatted: function () {
                return this.formatMetricValue(this.valueMetric, this.prevPeriodDiff);
            },

            prevPeriodDiffFormattedAbs: function () {
                return this.formatMetricValue(this.valueMetric, Math.abs(this.prevPeriodDiff));
            },

            linkHref: function () {
                if (this.isIndicatorDimension) {
                    return this.getWidgetDataForMetric('link_href', '');
                }

                return this.configuration.link_href;
            },

            linkHrefProcessed: function () {
                if (this.linkHref === 'popup') {
                    return '#';
                }

                return this.linkHref;
            },

            complicationClass: function () {
                if (this.isIndicatorDimension) {
                    return this.getWidgetDataForMetric('icon_complication', '');
                }

                return null;
            }
        },
        methods: {
            getRequestDimension: function () {
                return this.widget.configuration.dimension;
            },

            extendConfigurationBeforeDataFetch: function (widgetConfiguration) {
                if (this.isIndicatorDimension) {
                    return;
                }

                // In the indicator widget we rely on metric totals
                //
                widgetConfiguration.metrics = [
                    {
                        metric: this.valueMetric,
                        display_totals: 1
                    }
                ];

                widgetConfiguration.records_per_page = 1;
            },

            getRequestMetrics: function () {
                if (this.isIndicatorDimension) {
                    return [
                        'icon_status',
                        'icon_complication',
                        'value',
                        'link_enabled',
                        'link_href'
                    ];
                }
                else {
                    return [
                        this.valueMetric
                    ]
                }
            },

            makeDefaultConfigAndData: function () {
                if (this.widget.configuration.title === undefined) {
                    Vue.set(this.widget.configuration, 'title', 'Indicator');
                }

                if (this.widget.configuration.icon === undefined) {
                    Vue.set(this.widget.configuration, 'icon', 'ph ph-sun');
                }

                Vue.set(this.widget, 'loadedValue', {
                    "oc_metric_value": "No Value",
                    "icon_status": "disabled"
                });
            },

            getSettingsConfiguration: function () {
                const result = [];
                this.addTitleConfigurationProp(result);

                const metricsVisibility = (obj) => {
                    return !String(obj.dimension).startsWith('indicator@') &&
                        !$.oc.vueComponentHelpers.inspector.utils.isValueEmpty(obj.dimension);
                }

                const linkTextVisibility = (obj) => {
                    if (!metricsVisibility(obj)) {
                        return false;
                    }

                    return !$.oc.vueComponentHelpers.inspector.utils.isValueEmpty(obj.link_text);
                }

                result.push({
                    property: 'icon',
                    title: this.trans('prop-icon'),
                    tab: this.trans('tab-general'),
                    type: "dropdown",
                    options: phIconList,
                    useValuesAsIcons: true,
                    validation: {
                        required: {
                            message: this.trans('prop-icon-required'),
                        }
                    }
                });

                this.addDataSourceProps(result, this.trans('tab-general'), ['indicator']);
                this.addDataSourceConfigurationProps(result, ['auto_update']);

                const metricsCacheKey = 'ds-metrics';
                const metricsCachePropertyNames = ['data_source', 'dimension'];

                result.push({
                    property: 'metric',
                    title: this.trans('prop-metric-value'),
                    tab: this.trans('tab-general'),
                    default: 'value',
                    type: "dropdown",
                    dataCacheKeyName: metricsCacheKey,
                    dataCacheKeyPropertyNames: metricsCachePropertyNames,
                    depends: ['data_source', 'dimension'],
                    serverClassName: 'Backend\\Controllers\\Index\\DashboardHandler',
                    visibility: metricsVisibility
                });

                result.push({
                    property: 'icon_status',
                    default: 'icon_status',
                    title: this.trans('prop-icon-status'),
                    tab: this.trans('tab-general'),
                    type: "dropdown",
                    options: {
                        'status-info': this.trans('icon-status-info'),
                        'status-important': this.trans('icon-status-important'),
                        'status-success': this.trans('icon-status-success'),
                        'status-warning': this.trans('icon-status-warning'),
                        'status-disabled': this.trans('icon-status-disabled'),
                    },
                    visibility: metricsVisibility
                });

                result.push({
                    property: "link_text",
                    title: this.trans('prop-link-text'),
                    tab: this.trans('tab-general'),
                    type: "string"
                });

                result.push({
                    property: 'link_href',
                    default: '',
                    title: this.trans('prop-href'),
                    tab: this.trans('tab-general'),
                    type: 'string',
                    visibility: linkTextVisibility,
                    no_focus_on_visible: true
                });

                this.addDataSourceConfigurationProps(result, ['filters', 'date_interval']);

                return result;
            },

            loadAndDisplayPopup: async function () {
                this.loadingPopupData = true;
                const responseData = await this.runDataSourceHandler('onGetPopupData');
                this.loadingPopupData = false;

                try {
                    await $.oc.vueComponentHelpers.modalUtils.showBasic(
                        responseData.title,
                        responseData.content
                    );
                }
                catch (err) {}
            },

            onLinkClick: function (ev) {
                if (this.linkHref === 'popup') {
                    ev.preventDefault();
                    ev.stopPropagation();
                    this.loadAndDisplayPopup();
                    return;
                }
            }
        },
        template: '#backend_vuecomponents_dashboard_widgetindicator'
    });
});