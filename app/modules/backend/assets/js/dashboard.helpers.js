oc.Modules.register('backend.vuecomponents.dashboard.helpers', function () {
    class Helpers {
        constructor() {
            this.lastKnownKey = 0;
        }

        dashboardHasUniqueKey(rows, key) {
            return rows.some((row) => {
                return row._unique_key === key ||
                    this.hasUniqueKey(row.widgets, key);
            })
        }

        hasUniqueKey(objects, key) {
            return objects.some(function(object) {
                return object._unique_key === key;
            });
        }

        makeUniqueKey(rows) {
            let uniqueKey = rows.length + this.lastKnownKey;

            while (this.dashboardHasUniqueKey(rows, uniqueKey)) {
                uniqueKey++;
            }

            this.lastKnownKey = uniqueKey;
        
            return uniqueKey;
        }

        setUniqueKeysForDashboard(rows) {
            rows.forEach(row => {
                row._unique_key = this.makeUniqueKey(rows);

                row.widgets.forEach(widget => {
                    widget._unique_key = this.makeUniqueKey(rows);
                })
            });
        }
    }

    return new Helpers();
});