oc.Modules.register('backend.component.dashboard', function () {
    Vue.component('backend-component-dashboard', {
        props: {
            store: Object,
            currentDashboard: Object,
        },
        data: function () {
            return {
                saving: false
            };
        },
        computed: {
        },
        methods: {
            async onApplyChanges() {
                const dashboardManager = oc.Modules.import('backend.vuecomponents.dashboard.manager');
                const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');

                this.saving = true;

                try {
                    await dashboardManager.saveDashboard(this.currentDashboard);
                    this.store.state.editMode = false;
                    $.oc.snackbar.show(dashboardPage.trans('dashboard-updated', this.$el));
                }
                catch (err) {
                    oc.alert(err.message);
                }
                finally {
                    this.saving = false;
                }
            },

            onCancelChanges: function () {
                this.store.cancelEditing();
            }
        },
        mounted: function onMounted() {
        },
        watch: {
        },
        beforeDestroy: function beforeDestroy() {
        },
        template: '#backend_vuecomponents_dashboard'
    });
});
