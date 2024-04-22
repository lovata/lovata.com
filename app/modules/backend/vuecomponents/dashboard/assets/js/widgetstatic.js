oc.Modules.register('backend.component.dashboard.widget.static', function () {
    Vue.component('backend-component-dashboard-widget-static', {
        extends: oc.Modules.import('backend.vuecomponents.dashboard.widget-base'),
        data: function () {
            return {
            }
        },
        methods: {
            getRequestDimension: function () {
                return 'none';
            },

            getRequestMetrics: function () {
                return [];
            }
        },
        template: '#backend_vuecomponents_dashboard_widgetstatic'
    });
});