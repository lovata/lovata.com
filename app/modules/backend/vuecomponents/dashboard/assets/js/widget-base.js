oc.Modules.register('backend.vuecomponents.dashboard.widget-base', function () {
    const dataHelper = oc.Modules.import('backend.vuecomponents.dashboard.datahelper');
    const InspectorConfigurator = oc.Modules.import('backend.vuecomponents.dashboard.inspector-configurator');
    const dataSource = oc.Modules.import('backend.vuecomponents.dashboard.datasource');
    
    return {
        props: {
            error: Boolean,
            widget: Object,
            store: Object,
            loading: Boolean,
            autoUpdating: Boolean
        },
        computed: {
            isConfigured: function () {
                return !!this.widget.configuration.data_source;
            },

            loadedValue: function () {
                return this.fullWidgetData ? this.fullWidgetData.current.widget_data : undefined;
            },

            totalRecords: function () {
                return this.fullWidgetData ? this.fullWidgetData.current.total_records : 0;
            },

            loadedValuePrev: function () {
                return this.fullWidgetData && this.fullWidgetData.previous ?
                    this.fullWidgetData.previous.widget_data :
                    undefined;
            },

            metricsData: function () {
                return this.fullWidgetData ? this.fullWidgetData.metrics_data : undefined;
            },

            dimensionFieldsData: function () {
                return this.fullWidgetData ? this.fullWidgetData.dimension_fields_data : undefined;
            },

            metricsTotals: function () {
                return this.fullWidgetData ? this.fullWidgetData.current.metric_totals : undefined;
            },

            metricsTotalsPrev: function () {
                return this.fullWidgetData && this.fullWidgetData.previous ?
                    this.fullWidgetData.previous.metric_totals :
                    undefined;
            },

            dimensionData: function () {
                return this.fullWidgetData ? this.fullWidgetData.dimension_data : undefined;
            },

            fullWidgetData: function () {
                return this.store.getWidgetDataForDashboard(
                    this.store.getCurrentDashboard(),
                    this.widget._unique_key
                );
            },

            configuration: function () {
                return this.widget.configuration;
            },

            showMetricsTotalRow: function () {
                if (!this.configuration.metrics) {
                    return;
                }

                let result = false;
                this.configuration.metrics.forEach(metricConfig => {
                    if (metricConfig.display_totals) {
                        result = true;
                    }
                });

                return result;
            },

            explicitLoading: function () {
                return this.loading && !this.autoUpdating;
            }
        },
        methods: {
            runDataSourceHandler(handlerName, extraData = {}) {
                const widgetConfiguration = $.oc.vueUtils.getCleanObject(this.widget.configuration);
                this.extendConfigurationBeforeDataFetch(widgetConfiguration);

                return dataSource.runDataSourceHandler(handlerName, widgetConfiguration, extraData);
            },

            request(handlerName, extraData = {}) {
                const widgetConfiguration = $.oc.vueUtils.getCleanObject(this.widget.configuration);
                this.extendConfigurationBeforeDataFetch(widgetConfiguration);

                return dataSource.runCustomWidgetHandler(handlerName, widgetConfiguration, extraData);
            },

            getRequestDimension: function () {
                throw new Error('getRequestDimension is not implemented');
            },

            getRequestMetrics: function () {
                throw new Error('getRequestMetrics is not implemented');
            },

            getRequestInterval: function (defaultInterval) {
                return defaultInterval;
            },

            extendConfigurationBeforeDataFetch: function (widgetConfiguration) {},

            getWidgetDataForMetricPeriod: function (metricCode, defaultValue, prefix, periodData) {
                if (!this.widget || !Array.isArray(periodData) || periodData.length < 1) {
                    return defaultValue;
                }
        
                let result = periodData[0]['oc_metric_' + metricCode];
                if (prefix) {
                    result = prefix + result;
                }
        
                return result;
            },

            getWidgetDataForMetric: function (metricCode, defaultValue, prefix) {
                return this.getWidgetDataForMetricPeriod(metricCode, defaultValue, prefix, this.loadedValue);
            },

            getWidgetDataForMetricPrev: function (metricCode, defaultValue, prefix) {
                return this.getWidgetDataForMetricPeriod(metricCode, defaultValue, prefix, this.loadedValuePrev);
            },

            getDimensionFieldName: function (dimensionFieldCode) {
                if (!this.dimensionFieldsData) {
                    return "";
                }

                return this.dimensionFieldsData[dimensionFieldCode];
            },

            getDimensionFieldValue: function (record, dimensionFieldCode) {
                return record[dimensionFieldCode];
            },

            reloadOnGroupIntervalChange: function () {
                return false;
            },

            getRequestExtraData: function () {
                return {
                    current_page: 0
                };
            },

            getSettingsConfiguration: function () {
                throw new Error('getSettingsConfiguration is not implemented');
            },

            useCustomData: function () {
                return false;
            },

            makeDefaultConfigAndData: function () {
                throw new Error('makeDefaultData is not implemented');  
            },

            getMetricTotalClean: function (metricCode, prevPeriod = false) {
                const totals = prevPeriod ? this.metricsTotalsPrev : this.metricsTotals;

                if (!totals || totals[metricCode] === null || totals[metricCode] === undefined) {
                    return null;
                }

                return parseFloat(totals[metricCode]);
            },

            formatMetricValue: function (metricCode, value) {
                return dataHelper.formatValue(
                    value,
                    this.getMetricIntlFormatOptions(metricCode),
                    this.store.state.locale
                );
            },

            getMetricTotal: function (metricCode, prevPeriod = false) {
                const total = this.getMetricTotalClean(metricCode, prevPeriod);
                if (total === null) {
                    return null;
                }

                return this.formatMetricValue(metricCode, total);
            },

            getMetricIntlFormatOptions: function (metricCode) {
                if (!this.metricsData) {
                    return;
                }

                const metric = this.metricsData[metricCode];
                if (!metric) {
                    return undefined;
                }

                return metric.format_options;
            },

            makeRandomWidth: function () {
                return Math.floor((0.3 + Math.random()*0.3) * 100) + '%';
            },

            trans(key) {
                const fullKey = 'data-lang-' + key;
                const result = this.$el.getAttribute(fullKey);
                if (typeof result === 'string') {
                    return result;
                }
                
                const parent = this.$el.closest('[data-report-widget]');
                if (parent) {
                    return parent.getAttribute(fullKey);
                }

                return null;
            },

            addTitleConfigurationProp: function (configuration, optional) {
                const prop = {
                    property: "title",
                    title: this.trans('prop-title'),
                    type: "string"
                };

                if (!optional) {
                    prop.validation = {
                        required: {
                            message: this.trans('prop-title-required'),
                        }
                    }
                }
                else {
                    prop.placeholder = this.trans('prop-title-optional-placeholder')
                }

                configuration.push(prop);
            },

            addDataSourceProps: function (configuration, tab, allowedDimensionTypes) {
                const configurator = new InspectorConfigurator(this.$el, this.trans, this.store);
                configurator.defineDataSource(configuration, tab, allowedDimensionTypes);
            },

            addDataSourceConfigurationProps: function(configuration, filter = [], suppress = []) {
                const configurator = new InspectorConfigurator(this.$el, this.trans, this.store);
                configurator.defineDataSourceProperties(configuration, filter, suppress);
            },

            getConfigurationPropIndex: function(configuration, propertyName) {
                return configuration.findIndex(obj => obj.property === propertyName);
            },

            findConfigurationProp: function(configuration, propertyName) {
                const index = this.getConfigurationPropIndex(configuration, propertyName)
                if (index !== -1) {
                    return configuration[index];
                }

                return null;
            },

            onConfigurationUpdated: function() {},

            addConfigurationPropAfter: function(configuration, afterProperty, propConfiguration) {
                const index = this.getConfigurationPropIndex(configuration, afterProperty)
                
                if (index !== -1) {
                    configuration.splice(index + 1, 0, propConfiguration);
                } else {
                    throw new Error('Property ' + afterProperty + ' not found in the Inspector configuration');
                }
            },

            addConfigurationPropBefore: function(configuration, beforeProperty, propConfiguration) {
                const index = this.getConfigurationPropIndex(configuration, beforeProperty)
                
                if (index !== -1) {
                    configuration.splice(index, 0, propConfiguration);
                } else {
                    throw new Error('Property ' + afterProperty + ' not found in the Inspector configuration');
                }
            }
        }
    };
});
