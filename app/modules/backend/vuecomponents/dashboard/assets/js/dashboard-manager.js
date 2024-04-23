oc.Modules.register('backend.vuecomponents.dashboard.manager', function () {
    class DashboardManager {
        constructor() {
        }

        saveDashboard(dashboard) {
            return new Promise(function (resolve, reject, onCancel) {
                const data = {
                    definition: JSON.stringify(dashboard.rows),
                    slug: dashboard.slug
                };

                $.request('onSaveDashboard', {
                    progressBar: true,
                    data: data,
                    success: function (data) {
                        resolve(data)
                    },
                    error: function (err) {
                        reject(new Error(err))
                    }
                });
            });
        }

        createDashboard(data, store) {
            return new Promise(function (resolve, reject) {
                $.request('onCreateDashboard', {
                    progressBar: true,
                    data: data,
                    success: function (response) {
                        store.setDashboards(response.dashboards);

                        resolve();
                    },
                    error: function (err) {
                        reject(new Error(err))
                    }
                });
            });
        }

        updateDashboardConfig(data, store) {
            const requestData = data;
            requestData.original_slug = store.state.dashboardSlug;

            return new Promise(function (resolve, reject) {
                $.request('onUpdateDashboardConfig', {
                    progressBar: true,
                    data: data,
                    success: function (response) {
                        store.setDashboards(response.dashboards);

                        resolve();
                    },
                    error: function (err) {
                        reject(new Error(err))
                    }
                });
            });
        }

        deleteDashboard(store) {
            const requestData = {
                slug: store.state.dashboardSlug
            };

            return new Promise(function (resolve, reject) {
                $.request('onDeleteDashboard', {
                    progressBar: true,
                    data: requestData,
                    success: function (response) {
                        store.setDashboards(response.dashboards);

                        resolve();
                    },
                    error: function (err) {
                        reject(new Error(err))
                    }
                });
            });
        }
    }

    return new DashboardManager();
});