oc.Modules.register('backend.component.dashboard.widget.textnotice', function () {
    Vue.component('backend-component-dashboard-widget-textnotice', {
        extends: oc.Modules.import('backend.vuecomponents.dashboard.widget-base'),
        data: function () {
            return {
            }
        },
        computed: {
        },
        methods: {
            makeDefaultConfigAndData: function () {
                const sizing = oc.Modules.import('backend.vuecomponents.dashboard.sizing');

                Vue.set(this.widget.configuration, 'title', 'Text Notice');
                Vue.set(this.widget.configuration, 'notice', 'This is a text notice widget.');
            },

            getSettingsConfiguration: function () {
                const result = [{
                    property: "title",
                    title: this.trans('prop-title'),
                    type: "string",
                    validation: {
                        required: {
                            message: this.trans('prop-title-required'),
                        }
                    }
                }];

                result.push({
                    property: "notice",
                    title: this.trans('prop-notice-text'),
                    type: "text",
                });

                return result;
            }
        },
        template: '#backend_vuecomponents_dashboard_widgettextnotice'
    });
});