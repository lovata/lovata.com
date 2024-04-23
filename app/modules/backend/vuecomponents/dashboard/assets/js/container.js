oc.Modules.register('backend.component.dashboard.container', function () {
    const phIconList = oc.Modules.import('backend.phosphor-icon-list');

    Vue.component('backend-component-dashboard-container', {
        props: {
            store: Object
        },
        data: function () {
            return {
                dashboardRefreshKey: 0
            };
        },
        computed: {
            currentDashboard: function () {
                return this.store.getCurrentDashboard();
            },

            canCreateAndEdit: function () {
                return this.store.state.canCreateAndEdit;
            }
        },
        methods: {
            patchInspectorConfig: function (config) {
                config.forEach((item) => {
                    if (item.property === "icon") {
                        item.options = phIconList;
                    }
                })

                return config;
            },

            onCreateDashboard: function () {
                const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');
                const dashboardManager = oc.Modules.import('backend.vuecomponents.dashboard.manager');

                const inspectorConfig = $.oc.vueUtils.getCleanObject(this.store.inspectorConfigs.dashboardCreateUpdate);
                const config = this.patchInspectorConfig(inspectorConfig.config);
                const dataHolder = {};

                $.oc.vueComponentHelpers.inspector.host
                    .showModal(
                        inspectorConfig.title_create,
                        dataHolder,
                        config,
                        'dashboard-configuration',
                        {
                            buttonText: dashboardPage.trans('apply', this.$el),
                            resizableWidth: true,
                            beforeApplyCallback: async (updatedData) => {
                                try {
                                    await dashboardManager.createDashboard(updatedData, this.store);
                                }
                                catch (err) {
                                    oc.alert(err.message);
                                    throw err;
                                }

                                $.oc.snackbar.show(inspectorConfig.confirmation_created);
                            }
                        }
                    )
                    .then(() => {
                        this.store.state.editMode = true;
                        this.navigateToDashboard(dataHolder.slug, false);
                    }, $.noop);
            },

            onUpdateDashboard: function () {
                const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');
                const dashboardManager = oc.Modules.import('backend.vuecomponents.dashboard.manager');

                const inspectorConfig = $.oc.vueUtils.getCleanObject(this.store.inspectorConfigs.dashboardCreateUpdate);
                const config = this.patchInspectorConfig(inspectorConfig.config);
                const dataHolder = $.oc.vueUtils.getCleanObject(this.store.getCurrentDashboard());

                $.oc.vueComponentHelpers.inspector.host
                    .showModal(
                        inspectorConfig.title_edit,
                        dataHolder,
                        config,
                        'dashboard-configuration',
                        {
                            buttonText: dashboardPage.trans('apply', this.$el),
                            resizableWidth: true,
                            beforeApplyCallback: async (updatedData) => {
                                try {
                                    await dashboardManager.updateDashboardConfig(updatedData, this.store);
                                }
                                catch (err) {
                                    oc.alert(err.message);
                                    throw err;
                                }

                                $.oc.snackbar.show(inspectorConfig.confirmation_updated);
                            }
                        }
                    )
                    .then(() => {
                        if (dataHolder.slug !== this.store.state.dashboardSlug) {
                            this.navigateToDashboard(dataHolder.slug, true);
                        }
                    }, $.noop);
            },

            onDeleteDashboard: function () {
                const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');
                const dashboardManager = oc.Modules.import('backend.vuecomponents.dashboard.manager');

                oc.confirm(dashboardPage.trans('delete-confirm', this.$el), async (isConfirm) => {
                    if (!isConfirm) {
                        return;
                    }

                    try {
                        await dashboardManager.deleteDashboard(this.store);
                        $.oc.snackbar.show(dashboardPage.trans('delete-success', this.$el));
                    }
                    catch (err) {
                        oc.alert(err.message);
                        throw err;
                    }

                    this.navigateToDashboard("", true);
                });
            },

            onImportDashboard: function () {
                this.$refs.fileInput.click();
            },

            onImportFileChange: function () {
                const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');

                const $form = $(this.$refs.fileInputForm);
                $form.request('onUploadDashboard', {
                    files: $form.find('input'),
                    success: (data) => {
                        this.$refs.fileInput.value = null;
                        this.store.setDashboards(data.dashboards);
                        this.navigateToDashboard(data.slug, false);
                        $.oc.snackbar.show(dashboardPage.trans('import-success', this.$el));
                    },
                    error: (err) => {
                        oc.alert(err);
                        this.$refs.fileInput.value = null;
                    }
                });

            },

            navigateToDashboard: function (slug, replace) {
                const methodName = replace ? 'replace' : 'push';

                this.$router[methodName]({
                    name: 'dashboard',
                    query: { ...this.$route.query, dashboard: slug }
                }).catch(err => {
                    if (err.name !== 'NavigationDuplicated') {
                        throw err;
                    }
                });

            }
        },
        mounted: function onMounted() {
        },
        watch: {
            currentDashboard: function () {
                this.dashboardRefreshKey++;
            }
        },
        beforeDestroy: function beforeDestroy() {
        },
        template: '#backend_vuecomponents_dashboard_container'
    });
});
