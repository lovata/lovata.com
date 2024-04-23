oc.Modules.register('backend.component.dashboard.widget.chart', function () {
    const dataHelper = oc.Modules.import('backend.vuecomponents.dashboard.datahelper');

    function formatInterval(interval, date) {
        if (interval === 'month') {
            return date.format('MMM, YYYY');
        }

        if (interval === 'quarter') {
            return date.format('[Q]Q YYYY');
        }

        if (interval === 'year') {
            return date.format('YYYY');
        }

        return date.format('MMM D, YYYY');
    }

    Vue.component('backend-component-dashboard-widget-chart', {
        extends: oc.Modules.import('backend.vuecomponents.dashboard.widget-base'),
        data: function () {
            return {
                chart: null,
                lastGroupInterval: null
            }
        },
        computed: {
            chartConfig: function () {
                const theme = $('html').data('bs-theme');
                const axisColor = theme === 'dark' ? '#6C757D' : '#E3EAEC';

                const interval = this.store.state.range.interval;
                const isDateDimension = this.configuration.dimension === 'date';
                let xAxisType = isDateDimension ? 'time' : 'category';
                // if (isDateDimension && this.store.state.range.interval !== 'day') {
                    xAxisType = 'category';
                // }

                const reverseXAxis = xAxisType === 'time' &&
                    this.configuration.sort_by === 'oc_dimension' &&
                    this.configuration.sort_order === 'desc';

                const metricsData = this.metricsData;
                const chartType = this.configuration.chart_type;
                const barChartType = chartType === 'bar' || chartType === 'stacked-bar';
                let chartJsType = 'line';
                if (barChartType) {
                    chartJsType = 'bar';
                }

                let indexAxis = 'x';
                if (barChartType && this.configuration.bar_direction === 'horizontal') {
                    indexAxis = 'y';
                }

                const metrics = this.getRequestMetrics();
                const datasets = [];

                metrics.forEach(metric => {
                    const metricConfiguration = this.getMetricConfigurationByCode(metric);
                    const lineColor = metricConfiguration ? metricConfiguration.color : '#6A6CF7';
                    const bgColor = barChartType ? lineColor : dataHelper.hexToRgbaBackground(lineColor);

                    datasets.push({
                        axis: indexAxis,
                        data: [],
                        label: metricsData[metric].label,
                        pointBackgroundColor: lineColor,
                        backgroundColor: bgColor,
                        borderColor: lineColor,
                        tension: 0.3,
                        formatting: this.getMetricIntlFormatOptions(metric)
                    });
                })

                const result = {
                    type: chartJsType,
                    data: {
                        labels: [],
                        datasets: datasets
                    },
                    options: {
                        indexAxis: indexAxis,
                        datasets: {
                            line: {
                                fill: true,
                                pointRadius: 2,
                                borderWidth: 2,
                            },
                            bar: {
                                borderRadius: 5,
                                borderWidth: 1,
                            }
                        },
                        maintainAspectRatio: false,
                        animation: false,
                        responsive: true,
                        layout: {
                            padding: {
                                top: 30
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                cornerRadius: 2,
                                callbacks: {
                                    title: function (tooltipItem) {
                                        if (isDateDimension) {
                                            return formatInterval(interval, moment(tooltipItem[0].label));
                                        }
    
                                        return tooltipItem[0].label;
                                    },
                                    labelColor: function(tooltipItem, chart) {
                                        return {
                                            borderWidth: 0,
                                            backgroundColor: datasets[tooltipItem.datasetIndex].borderColor,
                                            borderRadius: 2
                                        };
                                    },
                                    label: (context) => {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            const valueAxis = indexAxis === 'x' ? 'y' : 'x';

                                            label += dataHelper.formatValue(
                                                context.parsed[valueAxis],
                                                context.dataset.formatting,
                                                this.store.state.locale
                                            )
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                border: {
                                    color: axisColor,
                                    dash: [2, 2]
                                },
                                time: {
                                    parser: 'YYYY-MM-DD',
                                    displayFormats: {
                                        day: 'MMM D, YYYY'
                                    }
                                },
                                ticks: {
                                    autoSkip: true,
                                    maxRotation: 0,
                                    minRotation: 0,
                                    callback: function (value, index, values) {
                                        if (!isDateDimension) {
                                            return this.getLabelForValue(value);
                                        }

                                        let date = xAxisType === 'time' ?
                                            moment(value) :
                                            moment(this.getLabelForValue(value), "YYYY-MM-DD");
                                        
                                        return formatInterval(interval, date);
                                    }
                                },
                               stacked: chartType === 'stacked-bar'
                            },
                            y: {
                                border: {
                                    color: axisColor,
                                    dash: [2, 2]
                                },
                                beginAtZero: true,
                                ticks: {
                                    maxTicksLimit: 5,
                                },
                               stacked: chartType === 'stacked-bar'
                            }
                        }
                    }
                };

                result.options.scales[indexAxis].type = xAxisType;
                result.options.scales[indexAxis].reverse = reverseXAxis;

                if (indexAxis === 'x') {
                    result.options.plugins.tooltip.mode = 'index';
                    result.options.plugins.tooltip.intersect = false;
                }

                return result;
            },
        },
        methods: {
            getRequestDimension: function () {
                return this.widget.configuration.dimension;
            },

            reloadOnGroupIntervalChange: function () {
                return true;
            },

            getRequestMetrics: function () {
                if (!Array.isArray(this.widget.configuration.metrics)) {
                    return [];
                }

                return this.widget.configuration.metrics.map(item => item.metric)
            },

            makeDefaultConfigAndData: function () {
                Vue.set(this.widget.configuration, 'title', 'Chart');
            },

            getMetricConfigurationByCode: function (metricCode) {
                if (!Array.isArray(this.widget.configuration.metrics)) {
                    return null;
                }

                const metrics = this.widget.configuration.metrics;
                for (let metricIndex = 0; metricIndex < metrics.length; metricIndex++) {
                    if (metrics[metricIndex].metric === metricCode) {
                        return metrics[metricIndex];
                    }
                }

                return null;
            },

            getSettingsConfiguration: function () {
                const result = [];
                this.addTitleConfigurationProp(result, true);

                result.push({
                    property: "chart_type",
                    tab: this.trans('tab-general'),
                    title: this.trans('prop-chart-type'),
                    type: "dropdown",
                    options: {
                        'bar': this.trans('chart-type-bar'),
                        'stacked-bar': this.trans('chart-type-stacked-bar'),
                        'line': this.trans('chart-type-line')
                    }
                });

                result.push({
                    property: "bar_direction",
                    tab: this.trans('tab-general'),
                    title: this.trans('prop-bar-direction'),
                    type: "dropdown",
                    default: 'vertical',
                    options: {
                        vertical: this.trans('bar-direction-vertical'),
                        horizontal: this.trans('bar-direction-horizontal'),
                    },
                    visibility: {
                        source_property: 'chart_type',
                        value: ['bar', 'stacked-bar']
                    },
                });

                this.addDataSourceProps(result, this.trans('tab-general'));
                this.addDataSourceConfigurationProps(result, [], ['auto_update']);

                return result;
            },

            buildChart: function () {
                const ctx = this.$refs.canvas.getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            },

            populateChart: function () {
                dataHelper.pushChartData(
                    this.chart.data,
                    this.loadedValue,
                    this.getRequestMetrics(),
                    true, // Null as zero
                    this.trans('value-not-set')
                );
                this.chart.update();
            }
        },
        mounted: function () {
            if (this.isConfigured && this.metricsData) {
                this.buildChart();

                // If the data already exists, which can happen if 
                // the chart was moved, populate the chart immediately.
                if (this.loadedValue) {
                    this.populateChart()
                }
            }
        },
        watch: {
            loadedValue: function () {
                if (!this.chart) {
                    this.buildChart();
                } else {
                    if (this.lastGroupInterval !== this.store.state.range.interval) {
                        this.chart.destroy();
                        this.buildChart();
                    }
                }

                this.lastGroupInterval = this.store.state.range.interval;

                this.populateChart();
            },
            configuration: {
                handler(newVal, oldVal) {
                    if (this.chart && this.isConfigured) {
                        this.chart.destroy();
                        this.buildChart();
                    }
                },
                deep: true
            }
        },
        beforeDestroy: function() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        },
        template: '#backend_vuecomponents_dashboard_widgetchart'
    });
});