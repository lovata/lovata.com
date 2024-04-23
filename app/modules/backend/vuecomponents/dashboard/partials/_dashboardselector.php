<div class="dashboard-selector"
    data-lang-edit-dashboard="<?= e(trans('backend::lang.dashboard.edit_dashboard')) ?>"
    data-lang-rename-dashboard="<?= e(trans('backend::lang.dashboard.rename_dashboard')) ?>"
    data-lang-delete-dashboard="<?= e(trans('backend::lang.dashboard.delete_dashboard')) ?>"
    data-lang-export-dashboard="<?= e(trans('backend::lang.dashboard.export_dashboard')) ?>"

    data-lang-new-dashboard="<?= e(trans('backend::lang.dashboard.new_dashboard')) ?>"
    data-lang-import-dashboard="<?= e(trans('backend::lang.dashboard.import_dashboard')) ?>"
>
    <div class="dashboard-list">
        <div class="dashboard-button-set">
            <a v-if="currentDashboard" href="#" class="dashboard-toolbar-button" @click.stop.prevent="onSelectDashboardClick">
                <i :class="currentDashboard.icon"></i>
                <span v-text="currentDashboard.name"></span>
                <i class="ph ph-caret-down"></i>
            </a>
            <a v-else href="#" class="dashboard-toolbar-button" @click.stop.prevent="onSelectDashboardClick">
                <span><?= e(trans('backend::lang.dashboard.select_dashboard')) ?></span>
                <i class="ph ph-caret-down"></i>
            </a>
        </div>

        <template v-if="dashboardDropdownVisible">
            <div class="backend-dropdownmenu-overlay" @click.prevent="onSelectorOverlayClick"></div>
            <div class="dropdown-items">
                <div class="items-container">
                    <router-link
                        v-for="dashboard in dashboards"
                        :key="dashboard._unique_key"
                        :to="{ name: 'dashboard', 
                            query: { ...$route.query, dashboard: dashboard.slug }
                        }"
                        class="dashboard-dropdown-item"
                        :class="{'selected': currentDashboardSlug === dashboard.slug}"
                    >
                        <i :class="dashboard.icon"></i>
                        <span v-text="dashboard.name"></span>
                    </router-link>
                </div>
            </div>
        </template>
    </div>

    <div v-if="canCreateAndEdit" class="dashboard-button-set">
        <button
            v-if="embeddedInDashboard"
            class="dashboard-toolbar-button"
            @click.stop.prevent="onEditClick"
            aria-label="<?= e(trans('backend::lang.dashboard.edit_dashboard')) ?>"
            title="<?= e(trans('backend::lang.dashboard.edit_dashboard')) ?>"
        ><i class="ph ph-gear"></i></button>
        <button
            class="dashboard-toolbar-button"
            @click.stop.prevent="onCreateClick"
            aria-label="<?= e(trans('backend::lang.dashboard.create_dashboard')) ?>"
            title="<?= e(trans('backend::lang.dashboard.create_dashboard')) ?>"
        >
            <i class="ph ph-plus"></i>
            <span v-if="!embeddedInDashboard"><?= e(trans('backend::lang.dashboard.create_dashboard')) ?></span>
        </button>

        <backend-component-dropdownmenu
            :items="editMenuItems"
            ref="editMenu"
            @command="onEditMenuItemCommand"
        ></backend-component-dropdownmenu>

        <backend-component-dropdownmenu
            :items="createMenuItems"
            ref="createMenu"
            @command="onCreateMenuItemCommand"
        ></backend-component-dropdownmenu>
    </div>
</div>