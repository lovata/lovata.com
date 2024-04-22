oc.Modules.register('backend.component.dashboard.report', function () {
    const helpers = oc.Modules.import('backend.vuecomponents.dashboard.helpers');
    const dashboardDragging = oc.Modules.import('backend.vuecomponents.dashboard.dragging');

    const responsivePoints = {
        1200: 1,
        992: 2,
        768: 3,
        576: 4
    };

    Vue.component('backend-component-dashboard-report', {
        props: {
            rows: Array,
            store: Object
        },
        methods: {
            onAddRowClick: function () {
                this.rows.push({
                    _unique_key: helpers.makeUniqueKey(this.rows),
                    widgets: []
                });
            },

            onDeleteRow: function (index) {
                this.rows.splice(index, 1);
            }
        },
        data: function () {
            return {
                rowCounter: 0,
                activeResponsivePoints: [],
                resizeObserver: null
            }
        },
        computed: {
            cssClass: function () {
                const result = [];

                if (this.store.state.editMode) {
                    result.push('edit-mode');
                }
                else {
                    this.activeResponsivePoints.forEach((value) => {
                        result.push('responsive-point-' + value);
                    });
                }

                return result;
            }
        },
        mounted: function mounted() {
            dashboardDragging.setStore(this.store);

            this.resizeObserver = new ResizeObserver(entries => {
                for (let entry of entries) {
                    const { width } = entry.contentRect;
                    this.activeResponsivePoints.splice(0);
                    Object.keys(responsivePoints).forEach((pointWidth) => {
                        if (pointWidth >= width) {
                            this.activeResponsivePoints.push(responsivePoints[pointWidth]);
                        }
                    });
                }
            });
            
            this.resizeObserver.observe(this.$el);
        },
        beforeDestroy: function beforeDestroy() {
            if (this.resizeObserver) {
                this.resizeObserver.disconnect();
                this.resizeObserver = null;
            }
        },
        watch: {},
        template: '#backend_vuecomponents_dashboard_report'
    });
});