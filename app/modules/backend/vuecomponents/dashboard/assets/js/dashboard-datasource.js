oc.Modules.register('backend.vuecomponents.dashboard.datasource', function () {
    class DataSource {
        constructor() {
            Queue.configure(Promise);
            this.queue = new Queue(4, 10000);
            this.datasourceMetricCache = {};
        }

        loadData(dateRange, aggregationInterval, dimension, metrics, widgetConfig, resetCache, extraData, compare) {
            return this.queue.add(function () {
                return new Promise(function (resolve, reject, onCancel) {
                    const data = {
                        widget_config: widgetConfig,
                        date_start: dateRange.dateStart,
                        date_end: dateRange.dateEnd,
                        dimension: dimension,
                        metrics: metrics,
                        aggregation_interval: aggregationInterval,
                        reset_cache: resetCache,
                        compare: compare ?? '',
                        extra_data: extraData ?? {}
                    };

                    var request = $.request('onGetWidgetData', {
                        progressBar: false,
                        data: data,
                        success: function (data) {
                            resolve(data)
                        },
                        error: function (data) {
                            reject(new Error(data))
                        }
                    });

                    onCancel(function () {
                        request.abort()
                    });
                })
            })
        }

        loadCustomData(dateRange, aggregationInterval, widgetConfig, resetCache, extraData, compare) {
            return this.queue.add(function () {
                return new Promise(function (resolve, reject, onCancel) {
                    const data = {
                        widget_config: widgetConfig,
                        date_start: dateRange.dateStart,
                        date_end: dateRange.dateEnd,
                        aggregation_interval: aggregationInterval,
                        reset_cache: resetCache,
                        compare: compare ?? '',
                        extra_data: extraData ?? {}
                    };

                    var request = $.request('onGetWidgetCustomData', {
                        progressBar: false,
                        data: data,
                        success: function (data) {
                            resolve(data)
                        },
                        error: function (data) {
                            reject(new Error(data))
                        }
                    });

                    onCancel(function () {
                        request.abort()
                    });
                })
            })
        }

        runDataSourceHandler(handlerName, widgetConfig, extraData) {
            return this.queue.add(function() {
                return new Promise(function (resolve, reject, onCancel) {
                    const data = Object.assign({}, {
                        handler: handlerName,
                        widget_config: widgetConfig
                    }, extraData);

                    var request = $.request('onRunDataSourceHandler', {
                        progressBar: true,
                        data: data,
                        success: function (data) {
                            resolve(data)
                        },
                        error: function (data) {
                            reject(new Error(data))
                        }
                    });

                    onCancel(function () {
                        request.abort()
                    });
                })
            })
        }

        runCustomWidgetHandler(handlerName, widgetConfig, extraData) {
            return this.queue.add(function() {
                return new Promise(function (resolve, reject, onCancel) {
                    const data = {
                        handler: handlerName,
                        widget_config: widgetConfig,
                        extra_data: extraData
                    };

                    var request = $.request('onRunCustomWidgetHandler', {
                        progressBar: true,
                        data: data,
                        success: function (data) {
                            resolve(data)
                        },
                        error: function (data) {
                            reject(new Error(data))
                        }
                    });

                    onCancel(function () {
                        request.abort()
                    });
                })
            })
        }
    }

    return new DataSource();
});