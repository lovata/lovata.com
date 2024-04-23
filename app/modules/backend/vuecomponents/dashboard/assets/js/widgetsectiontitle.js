oc.Modules.register('backend.component.dashboard.widget.sectiontitle', function () {
    Vue.component('backend-component-dashboard-widget-sectiontitle', {
        extends: oc.Modules.import('backend.vuecomponents.dashboard.widget-base'),
        data: function () {
            return {
            }
        },
        computed: {
            title: function () {
                let result = this.widget.configuration.title;

                if (this.widget.configuration.show_interval) {
                    result += ": " + this.store.state.intervalName;
                }

                return result;
            }
        },
        methods: {
            makeDefaultConfigAndData: function () {
                const sizing = oc.Modules.import('backend.vuecomponents.dashboard.sizing');

                Vue.set(this.widget.configuration, 'title', 'Section');
                Vue.set(this.widget.configuration, 'show_interval', false);
                Vue.set(this.widget.configuration, 'width', sizing.totalColumns);
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
                    property: "show_interval",
                    title: this.trans('prop-show-interval'),
                    type: "checkbox"
                });

                return result;
            }
        },
        template: '#backend_vuecomponents_dashboard_widgetsectiontitle'
    });
});