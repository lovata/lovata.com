oc.Modules.register('backend.component.dashboard.report.row', function () {
    const dashboardDragging = oc.Modules.import('backend.vuecomponents.dashboard.dragging');
    const widgetManager = oc.Modules.import('backend.vuecomponents.dashboard.widget-manager');
    const dashboardSizing = oc.Modules.import('backend.vuecomponents.dashboard.sizing');
    const dashboardReordering = oc.Modules.import('backend.vuecomponents.dashboard.reordering');

    Vue.component('backend-component-dashboard-report-row', {
        props: {
            row: Object,
            rows: Array,
            store: Object,
            rowIndex: Number
        },
        data: function () {
            return {
                addWidgetItems: [],
                menuItems: []
            };
        },
        methods: {
            makeAddWidgetsMenuItems: function (result) {
                const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');

                const defaultConfigs = this.store.state.defaultWidgetConfigs;
                const widgetTypes = this.store.getAvailableWidgetTypes();
                widgetTypes.forEach((widgetType) => {
                    if (widgetType.type in defaultConfigs) {
                        const typeConfigs = defaultConfigs[widgetType.type];
                        const widgetTypeItems = [];
                        widgetTypeItems.push({
                            type: 'text',
                            disabled: !this.addWidgetEnabled || (this.hasWidgets && widgetType.fullWidth),
                            command: {
                                command: 'add-widget',
                                type: widgetType.type
                            },
                            label: dashboardPage.trans('item-custom', this.$el)
                        });

                        widgetTypeItems.push({
                            type: 'separator'
                        });
        
                        Object.keys(typeConfigs).forEach((dsName) => {
                            const dimensions = typeConfigs[dsName];
                            const dimensionItems = [];
                            dimensions.forEach((dimensionInfo) => {
                                dimensionItems.push({
                                    type: 'text',
                                    command: {
                                        command: 'add-widget',
                                        type: widgetType.type,
                                        config: dimensionInfo.config,
                                        fullWidth: widgetType.fullWidth
                                    },
                                    label: dimensionInfo.dimension
                                });
                            });

                            widgetTypeItems.push({
                                type: 'text',
                                label: dsName,
                                items: dimensionItems
                            });
                        });

                        result.push({
                            type: 'text',
                            disabled: !this.addWidgetEnabled,
                            items: widgetTypeItems,
                            label: widgetType.name
                        });
                    }
                    else {
                        result.push({
                            type: 'text',
                            disabled: !this.addWidgetEnabled || (this.hasWidgets && widgetType.fullWidth),
                            command: {
                                command: 'add-widget',
                                type: widgetType.type,
                                fullWidth: widgetType.fullWidth
                            },
                            label: widgetType.name
                        });
                    }
                });

                const customGroupNames = Object.keys(this.store.state.customWidgetGroups);
                customGroupNames.forEach(groupName => {
                    const groupWidgets = this.store.state.customWidgetGroups[groupName];
                    
                    const groupItems = [];
                    groupWidgets.forEach(widgetData => {
                        groupItems.push({
                            type: 'text',
                            disabled: !this.addWidgetEnabled,
                            command: {
                                command: 'add-widget',
                                type: widgetData.type
                            },
                            label: widgetData.name
                        });
                    });
                    
                    result.push({
                        type: 'text',
                        label: groupName,
                        items: groupItems
                    });
                });
            },

            makeAddWidgetsMenu: function () {
                this.addWidgetItems = [];
                this.makeAddWidgetsMenuItems(this.addWidgetItems);
            },

            makeMenuItems: function () {
                const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');
                this.menuItems = []
                this.makeAddWidgetsMenuItems(this.menuItems);
                this.menuItems.push({
                    type: 'separator'
                });

                this.menuItems.push({
                    type: 'text',
                    command: 'delete',
                    label: dashboardPage.trans('item-delete-row', this.$el)
                });
            },

            onMenuItemCommand: function (command) {
                // Let the context menu hide before deleting the row
                Vue.nextTick(() => {
                    if (typeof command === 'object' && command.command === 'add-widget') {
                        widgetManager.createWidget(this.store, this.rows, this.row, command.type, command.config, command.fullWidth);
                    }
    
                    if (command === 'delete') {
                        this.$emit('deleteRow');
                    }
                });
            },

            onContextMenu: function (ev) {
                ev.preventDefault();
                if (this.$el.classList.contains('reordering')) {
                    return;
                }

                this.makeMenuItems();
                this.$refs.menu.showMenu(ev);
            },
            onDrop: function (ev) {
                dashboardDragging.onDrop(ev);
            },
            onDragEnd: function (ev) {
                dashboardDragging.onDragEnd(this.$el);
            },
            onDragOver: function (ev) {
                dashboardDragging.onDragOverRow(ev, this.row);
            },
            onRowButtonMouseDown: function (ev) {
                dashboardReordering.onMouseDown(ev, this.row, this.rows);
            },
            onAddWidgetClick: function (ev) {
                ev.preventDefault();
                this.makeAddWidgetsMenu();
                this.$refs.addWidgetMenu.showMenu(ev);
            },
            onAddWidgetMenuItemCommand: function (command) {
                if (typeof command === 'object' && command.command === 'add-widget') {
                    widgetManager.createWidget(this.store, this.rows, this.row, command.type, command.config, command.fullWidth);
                }
            }
        },
        computed: {
            systemFlags: function () {
                return this.store.state.systemDataFlags[this.row._unique_key];
            },

            cssClass: function () {
                const result = [];

                if (this.row.settings && this.row.settings.equal_height_widgets) {
                    result.push('equal-height-widgets');
                }

                if (this.row.widgets) {
                    result.push(!this.row.widgets.length ? 'no-widgets' : 'widgets-' + this.row.widgets.length);
                }

                if (this.systemFlags && this.systemFlags.widgetDoesntFit) {
                    result.push('widget-doesnt-fit-animation');
                }

                return result.join(' ');
            },

            addWidgetEnabled: function () {
                return dashboardSizing.canFitNewMinSizeWidget(this.row.widgets);
            },

            hasWidgets: function () {
                return !!this.row.widgets.length;
            }
        },
        mounted: function mounted() {
        },
        beforeDestroy: function beforeDestroy() {
        },
        watch: {
        },
        template: '#backend_vuecomponents_dashboard_reportrow'
    });
});