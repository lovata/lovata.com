oc.Modules.register('backend.component.dashboard.dashboard.selector', function () {
    Vue.component('backend-component-dashboard-dashboard-selector', {
        props: {
            store: Object,
            embeddedInDashboard: Boolean
        },
        computed: {
            currentDashboardSlug: function () {
                return this.store.state.dashboardSlug;
            },

            dashboards: function() {
                return this.store.state.dashboards;
            },

            canCreateAndEdit: function () {
                return this.store.state.canCreateAndEdit;
            },

            currentDashboard: function () {
                return this.store.getCurrentDashboard();
            }
        },
        data: function () {
            return {
                editMenuItems: [],
                createMenuItems: [],
                dashboardDropdownVisible: false
            };
        },
        methods: {
            setEditMenuItems: function () {
                const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');
                this.editMenuItems = [
                    {
                        type: 'text',
                        command: 'edit',
                        label: dashboardPage.trans('edit-dashboard', this.$el)
                    },
                    {
                        type: 'text',
                        command: 'rename',
                        label: dashboardPage.trans('rename-dashboard', this.$el)
                    },
                    {
                        type: 'text',
                        command: 'delete',
                        label: dashboardPage.trans('delete-dashboard', this.$el)
                    },
                    {
                        type: 'separator'
                    },
                    {
                        type: 'text',
                        href: this.store.exportUrl + '/' + this.store.state.dashboardSlug,
                        target: '_blank',
                        label: dashboardPage.trans('export-dashboard', this.$el)
                    }
                ];
            },

            setCreateMenuItems: function () {
                const dashboardPage = oc.Modules.import('backend.dashboard.page.instance');
                this.createMenuItems = [
                    {
                        type: 'text',
                        command: 'create',
                        label: dashboardPage.trans('new-dashboard', this.$el)
                    },
                    {
                        type: 'text',
                        command: 'import',
                        label: dashboardPage.trans('import-dashboard', this.$el)
                    }
                ];
            },

            hideDropdown: function () {
                this.dashboardDropdownVisible = false;
                $(document.body).off('keydown.dashboard-selector', this.onKeyDown);
            },
            
            onEditClick: function (ev) {
                this.setEditMenuItems();
                this.$refs.editMenu.showMenu(ev);
            },

            onCreateClick: function (ev) {
                this.setCreateMenuItems();
                this.$refs.createMenu.showMenu(ev);
            },

            onEditMenuItemCommand: function (command) {
                // Let the dropdown menu to hide before
                // running the next operation.
                Vue.nextTick(() => {
                    if (command === 'edit') {
                        this.store.startEditing();
                    }
    
                    if (command === 'rename') {
                        this.$emit('updateDashboard');
                    }
    
                    if (command === 'delete') {
                        this.$emit('deleteDashboard');
                    }    
                })
            },

            onCreateMenuItemCommand: function (command) {
                if (command === 'create') {
                    this.$emit('createDashboard');
                }

                if (command === 'import') {
                    this.$emit('importDashboard');
                }
            },

            onSelectDashboardClick: function () {
                this.dashboardDropdownVisible = true;
                $(document.body).on('keydown.dashboard-selector', this.onKeyDown);
            },

            onSelectorOverlayClick: function () {
                this.hideDropdown();
            },

            onKeyDown: function onKeyDown(ev) {
                if (ev.keyCode == 27) {
                    this.hideDropdown();
                }
            },
        },
        mounted: function onMounted() {
        },
        watch: {
        },
        beforeDestroy: function beforeDestroy() {
            $(document.body).off('keydown.dashboard-selector', this.onKeyDown);
        },
        template: '#backend_vuecomponents_dashboard_dashboardselector'
    });
});