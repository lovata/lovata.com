oc.Modules.register('backend.vuecomponents.dashboard.inspector-configurator', function () {
    const dataLoader = oc.Modules.import('backend.component.inspector.dataloader');

    return class InspectorConfigurator {
        constructor($el, translationFn, store) {
            this.trans = translationFn;
            this.store = store;
            this.$el = $el;

            this.filterAttributeCacheKey = 'ds-filter-attribute';
            this.filterAttributeCachePropertyNames = ['data_source', 'dimension'];
            this.metricsCacheKey = 'ds-metrics';
            this.metricsCachePropertyNames = ['data_source', 'dimension'];
        }

        get filterOperations() {
            return {
                '=': this.trans('filter-operation-equal-to'),
                '>=': this.trans('filter-operation-greater-equal'),
                '<=': this.trans('filter-operation-less-equal'),
                '>': this.trans('filter-operation-greater'),
                '<': this.trans('filter-operation-less'),
                'string_starts_with': this.trans('filter-operation-starts-with'),
                'string_includes': this.trans('filter-operation-includes'),
                'one_of': this.trans('filter-operation-one-of'),
            }
        }

        defineDataSource(configuration, tab, allowedDimensionTypes) {
            const getDynamicOptionsExtraData = () => {
                const result = {};
                if (Array.isArray(allowedDimensionTypes)) {
                    result.allowed_dimension_types = allowedDimensionTypes.join(',');
                }

                return result;
            };

            configuration.push({
                property: 'data_source',
                tab: tab,
                title: this.trans('prop-data-source'),
                getDynamicOptionsExtraData: getDynamicOptionsExtraData,
                type: 'dropdown',
                serverClassName: 'Backend\\Controllers\\Index\\DashboardHandler',
                validation: {
                    required: {
                        message: this.trans('prop-data-source-required'),
                    }
                }
            });

            configuration.push({
                property: 'dimension',
                tab: tab,
                title: this.trans('prop-dimension'),
                getDynamicOptionsExtraData: getDynamicOptionsExtraData,
                type: 'dropdown',
                serverClassName: 'Backend\\Controllers\\Index\\DashboardHandler',
                depends: ['data_source'],
                dataCacheKeyName: 'ds-dimensions',
                dataCacheKeyPropertyNames: ['data_source'],
                validation: {
                    required: {
                        message: this.trans('prop-dimension-required'),
                    }
                }
            });
        }

        propVisible(filter, suppress, propName) {
            const result = !filter.length || filter.includes(propName);
            if (!result) {
                return result;
            }

            return !suppress.includes(propName);
        }

        deletePropertyByName(props, name) {
            const index = props.findIndex(obj => obj.property === name);
            if (index > -1) {
                props.splice(index, 1);
            }
        }

        defineDataSourceProperties(configuration, filter = [], suppress = []) {
            this.propVisible(filter, suppress, 'metrics') && configuration.push({
                property: 'metrics',
                title: this.trans('prop-metrics'),
                tab: this.trans('tab-general'),
                type: 'objectList',
                titleProperty: 'metric',
                formatItemTitle: async (item, obj, parentObj) => this.formatMetricItemTitle(item, obj, parentObj),
                colorProperty: 'color',
                depends: ['data_source', 'dimension'],
                itemProperties: [
                    {
                        property: 'metric',
                        title: this.trans('prop-metric'),
                        type: 'dropdown',
                        dataCacheKeyName: this.metricsCacheKey,
                        dataCacheKeyPropertyNames: this.metricsCachePropertyNames,
                        serverClassName: 'Backend\\Controllers\\Index\\DashboardHandler',
                        validation: {
                            required: {
                                message: this.trans('prop-metric-required'),
                            }
                        }
                    },
                    {
                        property: 'color',
                        title: this.trans('prop-color'),
                        type: 'dropdown',
                        options: this.store.state.colors,
                        useValuesAsColors: true,
                        validation: {
                            required: {
                                message: this.trans('color-required'),
                            }
                        }
                    },
                    {
                        property: 'display_totals',
                        title: this.trans('prop-display-totals'),
                        type: 'checkbox'
                    },
                ],
                validation: {
                    required: {
                        message: this.trans('prop-metric-required'),
                    }
                }
            });

            this.propVisible(filter, suppress, 'limit') && configuration.push({
                tab: this.trans('tab-sorting-filtering'),
                property: 'limit',
                title: this.trans('prop-limit'),
                placeholder: this.trans('prop-limit-placeholder'),
                type: 'string',
                validation: {
                    integer: {
                        allowNegative: false,
                        message: this.trans('prop-limit-number'),
                        min: {
                            value: 1,
                            message: this.trans('prop-limit-min')
                        }
                    }
                }
            });

            this.propVisible(filter, suppress, 'empty_dimension_values') && configuration.push({
                tab: this.trans('tab-sorting-filtering'),
                property: 'empty_dimension_values',
                group: this.trans('empty-values'),
                title: this.trans('prop-empty-dimension'),
                default: 'not-set',
                type: 'dropdown',
                options: {
                    'not-set': this.trans('empty-values-display-not-set'),
                    'hide': this.trans('empty-values-hide')
                }
            });

            this.propVisible(filter, suppress, 'sort_by') && configuration.push({
                tab: this.trans('tab-sorting-filtering'),
                property: 'sort_by',
                group: this.trans('group-sorting'),
                title: this.trans('prop-sort-by'),
                type: "dropdown",
                default: 'oc_dimension',
                placeholder: this.trans('sort-by-placeholder'),
                depends: ['data_source', 'dimension', 'metrics'],
                serverClassName: 'Backend\\Controllers\\Index\\DashboardHandler',
                dataCacheKeyName: 'ds-sort-by',
                dataCacheKeyPropertyNames: ['data_source', 'dimension', 'metrics'],
                validation: {
                    required: {
                        message: this.trans('sort-by-required'),
                    }
                }
            });

            this.propVisible(filter, suppress, 'sort_order') && configuration.push({
                tab: this.trans('tab-sorting-filtering'),
                property: 'sort_order',
                group: this.trans('group-sorting'),
                title: this.trans('prop-sort-order'),
                type: "dropdown",
                default: 'asc',
                options: {
                    asc: this.trans('sort-asc'),
                    desc: this.trans('sort-desc')
                }
            });

            this.propVisible(filter, suppress, 'date_interval') && configuration.push({
                tab: this.trans('tab-sorting-filtering'),
                property: 'date_interval',
                group: this.trans('date-interval'),
                title: this.trans('prop-date-interval'),
                type: "dropdown",
                default: 'dashboard',
                options: {
                    dashboard: this.trans('date-interval-dashboard-default'),
                    year: this.trans('date-interval-this-year'),
                    quarter: this.trans('date-interval-this-quarter'),
                    month: this.trans('date-interval-this-month'),
                    week: this.trans('date-interval-this-week'),
                    hour: this.trans('date-interval-past-hour'),
                    days: this.trans('date-interval-past-days')
                }
            });

            this.propVisible(filter, suppress, 'date_interval') && configuration.push({
                tab: this.trans('tab-sorting-filtering'),
                property: 'date_interval_days',
                title: this.trans('prop-past-days-value'),
                group: this.trans('date-interval'),
                placeholder: this.trans('date-interval-past-days-placeholder'),
                type: 'string',
                visibility: {
                    source_property: 'date_interval',
                    value: 'days'
                },
                validation: {
                    integer: {
                        allowNegative: false,
                        message: this.trans('date-interval-past-days-invalid'),
                        min: {
                            value: 1,
                            message: this.trans('date-interval-past-days-invalid')
                        }
                    }
                }
            });

            this.propVisible(filter, suppress, 'auto_update') && configuration.push({
                tab: this.trans('tab-sorting-filtering'),
                property: 'auto_update',
                title: this.trans('prop-auto-update'),
                type: 'checkbox',
            });

            this.propVisible(filter, suppress, 'filters') && configuration.push({
                tab: this.trans('tab-sorting-filtering'),
                property: 'filters',
                title: this.trans('prop-filters'),
                type: 'objectList',
                titleProperty: 'filter_attribute',
                formatItemTitle: async (item, obj, parentObj) => this.formatFilterItemTitle(item, obj, parentObj),
                depends: ['data_source', 'dimension'],
                itemProperties: [
                    {
                        property: 'filter_attribute',
                        title: this.trans('prop-filter-attribute'),
                        type: 'dropdown',
                        dataCacheKeyName: this.filterAttributeCacheKey,
                        dataCacheKeyPropertyNames: this.filterAttributeCachePropertyNames,
                        serverClassName: 'Backend\\Controllers\\Index\\DashboardHandler',
                        validation: {
                            required: {
                                message: this.trans('filter-select-attribute')
                            }
                        }
                    },
                    {
                        property: 'operation',
                        title: this.trans('prop-operation'),
                        type: 'dropdown',
                        options: this.filterOperations,
                        validation: {
                            required: {
                                message: this.trans('filter-select-operation')
                            }
                        }
                    },
                    {
                        property: 'value_scalar',
                        title: this.trans('prop-value'),
                        type: 'string',
                        visibility: {
                            source_property: 'operation',
                            value: 'one_of',
                            inverse: true
                        }
                    },
                    {
                        property: 'value_array',
                        title: this.trans('prop-values'),
                        type: 'text',
                        description: this.trans('prop-values-one-per-line'),
                        visibility: {
                            source_property: 'operation',
                            value: 'one_of'
                        }
                    }
                ]
            });
        }

        async requestOptions(obj, parentObj, property, cacheKey, cachePropertyNames) {
            const serverClassName = 'Backend\\Controllers\\Index\\DashboardHandler';
            var data = Object.assign({}, $.oc.vueUtils.getCleanObject(parentObj), $.oc.vueUtils.getCleanObject(obj));
            data.inspectorProperty = property;
            data.inspectorClassName = serverClassName;

            const responseData = await dataLoader.requestOptions(
                this.$el,
                serverClassName,
                'onInspectableGetOptions',
                data,
                cacheKey,
                cachePropertyNames
            );

            if (!Array.isArray(responseData.options)) {
                throw new Error('onInspectableGetOptions must return an array');
            }

            const result = {};
            responseData.options.forEach(item => {
                result[item.value] = item.title;
            })

            return result;
        }

        async formatFilterItemTitle(item, obj, parentObj) {
            const options = await this.requestOptions(
                obj,
                parentObj,
                'filter_attribute',
                this.filterAttributeCacheKey,
                this.filterAttributeCachePropertyNames
            );

            const attributeName = options[item.filter_attribute];
            let operationName = this.filterOperations[item.operation];
            if (typeof operationName === "string") {
                operationName = operationName.toLowerCase();
            }

            if (item.operation !== 'one_of') {
                return attributeName + ' ' + operationName + ' "' + item.value_scalar + '"';
            }

            const valueArray = item.value_array
                .split('\n')
                .map(item => item.trim())
                .filter((item) => item.length > 0);

            return attributeName + ' ' + operationName + ' ' + '["'+valueArray.join('", "')+'"]';
        }

        async formatMetricItemTitle(item, obj, parentObj) {
            const options = await this.requestOptions(
                obj,
                parentObj,
                'metric',
                this.metricsCacheKey,
                this.metricsCachePropertyNames
            );

            return options[item.metric];
        }
    }
});