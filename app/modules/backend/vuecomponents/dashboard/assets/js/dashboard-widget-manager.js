oc.Modules.register('backend.vuecomponents.dashboard.widget-manager', function () {
    const dashboardSizing = oc.Modules.import('backend.vuecomponents.dashboard.sizing');

    class WidgetManager {
        constructor() {
        }

        createWidget(store, rows, row, type, defaultConfig, fullWidth) {
            if (fullWidth && row.widgets.length > 0) {
                return false;
            }

            const helpers = oc.Modules.import('backend.vuecomponents.dashboard.helpers');

            const totalRowWidgetsWidth = dashboardSizing.totalWidgetsWidth(row.widgets);
            const availableWidth = dashboardSizing.totalColumns - totalRowWidgetsWidth;

            if (availableWidth < dashboardSizing.minWidth) {
                return false;
            }

            const newWidgetWidth = fullWidth ? dashboardSizing.totalColumns : Math.min(availableWidth, 4);
            const configuration = {
                type: type
            };

            if (typeof defaultConfig === 'object') {
                Object.assign(configuration, defaultConfig);
            }

            const newWidget = {
                _unique_key: helpers.makeUniqueKey(rows),
                configuration: configuration,
                width: newWidgetWidth,
            };

            row.widgets.push(newWidget);
            store.setSystemDataFlag(newWidget, 'needsConfiguration', true)

            return true;
        }
    }

    return new WidgetManager();
});
