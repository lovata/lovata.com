oc.Modules.register('backend.component.dashboard.report.perioddiff', function () {
    const dataHelper = oc.Modules.import('backend.vuecomponents.dashboard.datahelper');
    Vue.component('backend-component-dashboard-report-diff', {
        props: {
            prevValue: Number,
            currentValue: Number,
            formattingOptions: Object,
            store: Object
        },
        methods: {
            formatValue: function (value) {
                return dataHelper.formatValue(
                    value,
                    this.formattingOptions,
                    this.store.state.locale
                );
            }
        },
        computed: {
            diff: function () {
                return this.currentValue - this.prevValue;
            },

            diffFormattedAbs: function () {
                return this.formatValue(Math.abs(this.diff));
            },

            diffFormatted: function () {
                return this.formatValue(this.diff);
            }
        },
        mounted: function mounted() {
        },
        beforeDestroy: function beforeDestroy() {
        },
        watch: {
        },
        template: '#backend_vuecomponents_dashboard_perioddiff'
    });
});