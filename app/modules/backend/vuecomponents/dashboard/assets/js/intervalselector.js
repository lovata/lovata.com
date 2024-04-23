oc.Modules.register('backend.component.dashboard.interval.selector', function () {
    const DashboardCalendar = oc.Modules.import('backend.vuecomponents.dashboard.calendar');
    let calendar = null;

    Vue.component('backend-component-dashboard-interval-selector', {
        props: {
            store: Object
        },
        data: function () {
            return {
                intervalMenuItems: [],
                compareMenuItems: [],
                groupingIntervalName: '',
                compareOptionName: ''
            }
        },
        computed: {
            intervalName: function () {
                return this.store.state.intervalName;
            },

            intervals: function () {
                const result = {};
                const codes = this.store.getValidIntervalCodes();
                codes.forEach((code) => {
                    result[code] = this.trans('interval-' + code);
                });

                return result;
            },

            compareOptions: function() {
                const result = {};
                const codes = this.store.getValidCompareCodes();
                codes.forEach((code) => {
                    result[code] = this.trans('compare-' + code);
                });

                return result;
            },

            rangeGroupingInterval: function () {
                return this.store.state.range.interval;
            },

            compareOption: function () {
                return this.store.state.compareMode;
            }
        },
        methods: {
            getStartDateObj: function () {
                return moment(this.store.state.range.dateStart, 'YYYY-MM-DD').toDate();
            },

            setIntervalMenuItems: function () {
                this.intervalMenuItems = [];

                for (let intervalCode in this.intervals) {
                    this.intervalMenuItems.push({
                        type: 'radiobutton',
                        command: intervalCode,
                        label: this.intervals[intervalCode],
                        checked: this.store.state.range.interval === intervalCode
                    });
                }
            },

            setCompareMenuItems: function () {
                this.compareMenuItems = [];
                for (let optionCode in this.compareOptions) {
                    this.compareMenuItems.push({
                        type: 'radiobutton',
                        command: optionCode,
                        label: this.compareOptions[optionCode],
                        checked: this.store.state.compareMode === optionCode
                    });
                }
            },

            onSelectIntervalClick: function (ev) {
                this.setIntervalMenuItems();
                this.$refs.intervalMenu.showMenu(ev);
            },

            onSelectCompareClick: function (ev) {
                this.setCompareMenuItems();
                this.$refs.compareMenu.showMenu(ev);
            },

            onIntervalMenuItemCommand: function (command) {
                this.$router.push({
                    name: 'dashboard',
                    params: {
                        ...this.$route.params,
                    },
                    query: {
                        ...this.$route.query,
                        interval: command
                    }
                }).catch(err => {
                    if (err.name !== 'NavigationDuplicated') {
                        throw err;
                    }
                });
            },

            getEndDateObj: function () {
                return moment(this.store.state.range.dateEnd, 'YYYY-MM-DD').toDate();
            },

            trans(key) {
                const fullKey = 'data-lang-' + key;
                const result = this.$el.getAttribute(fullKey);
                if (typeof result === 'string') {
                    return result;
                }
                
                return null;
            },

            updateCalendarRange: function () {
                var pickerControl = $(this.$refs.calendarControl).data('daterangepicker');
                pickerControl.setStartDate(this.getStartDateObj());
                pickerControl.setEndDate(this.getEndDateObj());
            },

            onCompareMenuItemCommand: function (command) {
                this.$router.push({
                    name: 'dashboard',
                    params: {
                        ...this.$route.params,
                    },
                    query: {
                        ...this.$route.query,
                        compare: command
                    }
                }).catch(err => {
                    if (err.name !== 'NavigationDuplicated') {
                        throw err;
                    }
                });
            },

            onApplyRange: function (ev, picker) {
                const startDate = picker.startDate.format('YYYY-MM-DD');
                const endDate = picker.endDate.format('YYYY-MM-DD');

                this.$router.push({
                    name: 'dashboard',
                    params: {
                        ...this.$route.params,
                    },
                    query: {
                        ...this.$route.query,
                        start: startDate,
                        end: endDate
                    }
                }).catch(err => {
                    if (err.name !== 'NavigationDuplicated') {
                        throw err;
                    }
                });
            }
        },
        mounted: function onMounted() {
            calendar = new DashboardCalendar(this.$refs.calendarControl, this.store.state.locale, this.trans);
            $(this.$refs.calendarControl).on('apply.daterangepicker', this.onApplyRange);
            this.updateCalendarRange();
        },
        watch: {
            intervalName: function() {
                this.updateCalendarRange();
            },
            rangeGroupingInterval: {
                immediate: true,
                handler(value) {
                    Vue.nextTick(() => {
                        const interval = this.intervals[value];
                        if (interval) {
                            this.groupingIntervalName = interval;
                        }
                    })
                }
            },
            compareOption: {
                immediate: true,
                handler(value) {
                    Vue.nextTick(() => {
                        const option = this.compareOptions[value];
                        if (option) {
                            this.compareOptionName = option;
                        }
                    })
                }
            }
        },
        beforeDestroy: function beforeDestroy() {
        },
        template: '#backend_vuecomponents_dashboard_intervalselector'
    });
});