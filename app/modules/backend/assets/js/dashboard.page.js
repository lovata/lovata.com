oc.Modules.register('backend.dashboard.page', function () {
    'use strict';

    const DashboardStore = oc.Modules.import('backend.dashboard.store');

    class DashboardPage {
        constructor() {
            this.store = new DashboardStore();
            this.universalDateFormat = 'YYYY-MM-DD';
            this.init();
        }

        init() {
            this.initVue();
        }

        initDefaultQueryParameters(to, next) {
            const requiredQueryParams = {
                start: moment().startOf('month').format(this.universalDateFormat),
                end: moment().format(this.universalDateFormat),
                interval: 'day',
                compare: 'none'
            };

            if (this.store.state.dashboards.length > 0) {
                requiredQueryParams.dashboard = this.store.state.dashboards[0].slug;
            }

            let isReplacingRoute = false;
            const newQueryParams = { ...to.query };

            for (const [key, defaultValue] of Object.entries(requiredQueryParams)) {
                if (!to.query.hasOwnProperty(key) || to.query[key] === "") {
                    newQueryParams[key] = defaultValue;
                    isReplacingRoute = true;
                }
            }

            if (newQueryParams.dashboard !== undefined && !this.store.state.dashboards.length) {
                delete newQueryParams.dashboard;
                isReplacingRoute = true;
            }

            if (isReplacingRoute) {
                next({
                    name: to.name,
                    params: to.params,
                    query: newQueryParams
                });

                return true;
            }

            return false;
        }

        makeRouter() {
            const router = new VueRouter({
                routes: [
                    {
                        name: 'dashboard',
                        path: '/',
                        component: Vue.options.components['backend-component-dashboard-container'],
                        props: () => ({ store: this.store })
                    }
                ]
            });

            router.beforeEach((to, from, next) => {
                if (this.initDefaultQueryParameters(to, next)) {
                    return;
                }

                if (
                    to.query.start !== from.query.start ||
                    to.query.end !== from.query.end ||
                    to.query.interval !== from.query.interval ||
                    to.query.compare !== from.query.compare ||
                    to.query.dashboard !== from.query.dashboard
                ) {
                    const dateStart = moment(to.query.start, this.universalDateFormat, true);
                    const dateEnd = moment(to.query.end, this.universalDateFormat, true);
                    if (
                        dateStart.isValid() &&
                        dateEnd.isValid() &&
                        this.store.isIntervalCodeValid(to.query.interval) &&
                        this.store.isCompareModeValid(to.query.compare)
                    ) {
                        this.store.state.range.dateStart = dateStart.format(this.universalDateFormat);
                        this.store.state.range.dateEnd = dateEnd.format(this.universalDateFormat);
                        this.store.state.range.interval = to.query.interval;
                        this.store.state.intervalName = this.makeIntervalName(dateStart.toDate(), dateEnd.toDate());
                        this.store.state.compareMode = to.query.compare;
                        this.store.state.dashboardSlug = to.query.dashboard;
                        this.store.resetData();

                        next();
                    }
                    else {
                        return false;
                    }
                }
                
                next();
            });

            return router;
        }

        makeIntervalName(startDate, endDate) {
            const startYear = startDate.getFullYear();
            const endYear = endDate.getFullYear();
        
            let formatterWithoutYear = new Intl.DateTimeFormat(this.store.state.locale, {
                month: 'short',
                day: 'numeric'
            });
        
            let formatterWithYear = new Intl.DateTimeFormat(this.store.state.locale, {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        
            const startFormatted = startYear === endYear
                ? formatterWithoutYear.format(startDate)
                : formatterWithYear.format(startDate);
        
            const endFormatted = formatterWithYear.format(endDate);
        
            return startFormatted + ' - ' + endFormatted;
        }

        initVue() {
            const initialState = $('#dashboard-initial-state').html();
            this.store.setInitialState(JSON.parse(initialState));

            this.vm = new Vue({
                data: {
                    store: this.store
                },
                router: this.makeRouter(),
                el: '#page-container'
            });
        }

        trans(key, $el) {
            const fullKey = 'data-lang-' + key;
            const result = $el.getAttribute(fullKey);
            if (typeof result === 'string') {
                return result;
            }

            return null;
        }
    }

    return DashboardPage;
});

document.addEventListener("DOMContentLoaded", () => {
    const DashboardPage = oc.Modules.import('backend.dashboard.page');
    const pageInstance = new DashboardPage();

    oc.Modules.register('backend.dashboard.page.instance', function () {
        return pageInstance;
    });
});