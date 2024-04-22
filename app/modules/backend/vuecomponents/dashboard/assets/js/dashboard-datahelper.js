oc.Modules.register('backend.vuecomponents.dashboard.datahelper', function () {
    class DataHelper {
        constructor() {
            this.numberFormats = {};
        }

        pushChartData(data, loadedData, metricNames, nullAsZero, notSetLabel) {
            data.labels.splice(0, data.labels.length);
            for (let metricIndex = 0; metricIndex < metricNames.length; metricIndex++) {
                const metricName = metricNames[metricIndex];
                const dataset = data.datasets[metricIndex];
                const fullMetricName = 'oc_metric_' + metricName;
                dataset.data.splice(0, dataset.data.length);

                loadedData.forEach(dataPoint => {
                    let value = dataPoint[fullMetricName];
                    if (value === null && nullAsZero) {
                        value = 0;
                    }

                    let dimensionValue = dataPoint.oc_dimension;
                    if (dataPoint.oc_dimension_label) {
                        dimensionValue = dataPoint.oc_dimension_label;
                    }

                    if (dimensionValue === null) {
                        dimensionValue = notSetLabel;
                    }

                    // dataset.data.push({
                    //     x: dimensionValue,
                    //     y: value,
                    // });

                    if (metricIndex === 0) {
                        data.labels.push(dimensionValue);
                    }

                    dataset.data.push(value);
                });
            }
        }

        hexToRgbaBackground(hex) {
            hex = hex.startsWith('#') ? hex.slice(1) : hex;

            const r = parseInt(hex.substring(0, 2), 16);
            const g = parseInt(hex.substring(2, 4), 16);
            const b = parseInt(hex.substring(4, 6), 16);
        
            return `rgba(${r}, ${g}, ${b}, 0.1)`;
        }

        formatValue(value, formatOptions, locale) {
            const cacheKey = JSON.stringify([locale, formatOptions]);
            if (!(cacheKey in this.numberFormats)) {
                this.numberFormats[cacheKey] = new Intl.NumberFormat(locale ?? undefined, formatOptions ?? undefined);
            }

            return this.numberFormats[cacheKey].format(value);
        }
    }

    return new DataHelper();
});