<div
    id="dashboard-container"
    class="oc-dashboard-container"
    :class="{'centered-container': !currentDashboard}"
    data-lang-apply="<?= e(trans('backend::lang.form.apply')) ?>"
    data-lang-delete-confirm="<?= e(trans('backend::lang.dashboard.delete_confirm')) ?>"
    data-lang-delete-success="<?= e(trans('backend::lang.dashboard.delete_success')) ?>"
    data-lang-widget-type-indicator="<?= e(trans('backend::lang.dashboard.widget_type_indicator')) ?>"
    data-lang-widget-type-section-title="<?= e(trans('backend::lang.dashboard.widget_type_section_title')) ?>"
    data-lang-widget-type-notice="<?= e(trans('backend::lang.dashboard.widget_type_notice')) ?>"
    
    data-lang-widget-type-chart="<?= e(trans('backend::lang.dashboard.widget_type_chart')) ?>"
    data-lang-widget-type-table="<?= e(trans('backend::lang.dashboard.widget_type_table')) ?>"
    data-lang-import-success="<?= e(trans('backend::lang.dashboard.import_success')) ?>"
>
    <template v-if="!currentDashboard">
        <div class="flex-layout-column full-height justify-center align-center">
            <template v-if="store.state.dashboards.length === 0">
                <div class="access-message" v-if="!canCreateAndEdit">
                    <?= e(trans('backend::lang.dashboard.no_access')) ?>
                </div>
                <template v-else>
                    <div class="access-message">
                        <?= e(trans('backend::lang.dashboard.no_dashboards')) ?>
                    </div>

                    <div class="oc-dashboard">
                        <div class="dashboard-toolbar-container">
                            <div class="dashboard-button-set">
                                <button
                                    class="dashboard-toolbar-button"
                                    @click.stop.prevent="onCreateDashboard()"
                                >
                                    <i class="ph ph-plus"></i>
                                    <span><?= e(trans('backend::lang.dashboard.create_dashboard')) ?></span>
                                </button>
                                <button
                                    class="dashboard-toolbar-button"
                                    @click.stop.prevent="onImportDashboard()"
                                >
                                    <i class="ph ph-upload"></i>
                                    <span><?= e(trans('backend::lang.dashboard.import_dashboard')) ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </template>

            <template v-else>
                <div class="access-message">
                    <?= e(trans('backend::lang.dashboard.not_found')) ?>
                </div>

                <div class="oc-dashboard">
                    <div class="dashboard-toolbar-container">
                        <backend-component-dashboard-dashboard-selector
                            :store="store"
                            @createDashboard="onCreateDashboard()"
                            @importDashboard="onImportDashboard()"
                        ></backend-component-dashboard-dashboard-selector>
                    </div>
                </div>
            </template>
        </div>
    </template>
    <backend-component-dashboard
        v-else
        :store="store"
        :key="dashboardRefreshKey"
        :currentDashboard="currentDashboard"
        @createDashboard="onCreateDashboard()"
        @updateDashboard="onUpdateDashboard()"
        @deleteDashboard="onDeleteDashboard()"
        @importDashboard="onImportDashboard()"
    ></backend-component-dashboard>

    <form ref="fileInputForm" data-request="onUploadDashboard" data-request-files v-show="false">
        <input type="file" ref="fileInput" name="file" accept=".json" @change="onImportFileChange">
    </form>
</div>