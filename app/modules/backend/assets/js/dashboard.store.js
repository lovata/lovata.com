oc.Modules.register('backend.dashboard.store', function () {
    'use strict';
    const helpers = oc.Modules.import('backend.vuecomponents.dashboard.helpers');

    class DashboardStore {
        state = {};

        constructor() {
            this.state = {
                locale: '',
                dashboards: [],
                dashboardSlug: null,
                colors: {},
                range: {
                    dateStart: null,
                    dateEnd: null,
                    interval: 'day'
                },
                intervalName: null,
                compareMode: 'none',
                editMode: false,
                canCreateAndEdit: false,
                defaultWidgetConfigs: {},
                customWidgetGroups: {},
                dashboardListScrollX: null,

                widgetData: {},
                systemDataFlags: {}
            };

            this.inspectorConfigs = {};
            this.dashboardUniqueKey = 0;
            this.exportUrl = "";
            this.dashboardBackup = null;
        }

        setInitialState(initialState) { 
            this.inspectorConfigs = initialState.inspectorConfigs;
            this.exportUrl = initialState.exportUrl;
            this.state.locale = initialState.locale;
            this.state.colors = initialState.colors;
            this.state.canCreateAndEdit = initialState.canCreateAndEdit;
            this.state.defaultWidgetConfigs = initialState.defaultWidgetConfigs;
            this.state.customWidgetGroups = initialState.customWidgetGroups;

            this.setDashboards(Array.isArray(initialState.dashboards) ? initialState.dashboards : []);
        }

        setDashboards(dashboards) {
            this.state.dashboards = dashboards;

            this.state.dashboards.forEach(dashboard => {
                this.initDashboardKey(dashboard);
            });
        }

        initDashboardKey(dashboard) {
            dashboard._unique_key = this.dashboardUniqueKey++;
            helpers.setUniqueKeysForDashboard(dashboard.rows);
        }

        getAvailableWidgetTypes() {
            const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');
            const containerEl = document.getElementById('dashboard-container');

            const result = [];
            result.push({
                type: 'indicator',
                name: dashboardPage.trans('widget-type-indicator', containerEl)
            },
            {
                type: 'section-title',
                name: dashboardPage.trans('widget-type-section-title', containerEl),
                fullWidth: true
            },
            {
                type: 'notice',
                name: dashboardPage.trans('widget-type-notice', containerEl),
                fullWidth: true
            },
            {
                type: 'chart',
                name: dashboardPage.trans('widget-type-chart', containerEl)
            },
            {
                type: 'table',
                name: dashboardPage.trans('widget-type-table', containerEl)
            });

            return result;
        }

        getValidIntervalCodes() {
            return ['day', 'week', 'month', 'quarter', 'year'];
        }

        getValidCompareCodes() {
            return ['prev-period', 'prev-year', 'none'];
        }

        isIntervalCodeValid(code) {
            return this.getValidIntervalCodes().includes(code);
        }

        isCompareModeValid(mode) {
            return this.getValidCompareCodes().includes(mode);
        }

        isDashboardSlugValid(slug) {
            return this.state.dashboards.some(function(item) {
                return item.slug === slug;
            });
        }

        getCurrentDashboard() {
            const state = this.state;
            if (state.dashboardSlug === null) {
                return null;
            }

            return state.dashboards.find((dashboard) => dashboard.slug === state.dashboardSlug);
        }

        setSystemDataFlag(widgetOrRow, flag, value) {
            const uniqueKey = widgetOrRow._unique_key;
            if (typeof this.state.systemDataFlags[uniqueKey] !== 'object') {
                Vue.set(this.state.systemDataFlags, uniqueKey, {});
            }

            Vue.set(this.state.systemDataFlags[uniqueKey], flag, value);
        }

        getSystemDataFlag(widgetOrRow, flag) {
            const uniqueKey = widgetOrRow._unique_key;
            if (typeof this.state.systemDataFlags[uniqueKey] !== 'object') {
                return undefined;
            }

            return this.state.systemDataFlags[uniqueKey][flag];
        }

        resetData() {
            // this.state.widgetData = {};
            this.state.systemDataFlags = {};
        }

        getWidgetDataForDashboard(dashboard, widgetKey) {
            const dashboardKey = dashboard._unique_key;
            if (dashboardKey === undefined) {
                throw new Error("Dashboard unique key is undefined");
            }

            if (this.state.widgetData[dashboardKey] === undefined) {
                return undefined;
            }

            return this.state.widgetData[dashboardKey][widgetKey];
        }

        unsetSystemDataFlag(widgetOrRow, flag) {
            const uniqueKey = widgetOrRow._unique_key;
            if (typeof this.state.systemDataFlags[uniqueKey] !== 'object') {
                return;
            }

            delete this.state.systemDataFlags[uniqueKey][flag];
        }

        startEditing() {
            this.dashboardBackup = $.oc.vueUtils.getCleanObject(this.getCurrentDashboard());
            this.state.editMode = true;
        }

        cancelEditing() {
            const currentDashboard = this.getCurrentDashboard();
            if (this.dashboardBackup && this.dashboardBackup.rows && currentDashboard) {
                Vue.set(currentDashboard, 'rows', this.dashboardBackup.rows);
            }
            this.state.editMode = false;
        }
    }

    return DashboardStore;
});
