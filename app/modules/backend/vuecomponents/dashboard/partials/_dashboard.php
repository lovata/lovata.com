<div
    class="oc-dashboard"
    data-lang-dashboard-updated="<?= e(trans('backend::lang.dashboard.updated_successfully')) ?>"
>
     <div class="dashboard-toolbar-container">
        <div class="dashboards">
            <backend-component-dashboard-dashboard-selector
                v-if="!store.state.editMode"
                :store="store"
                :embeddedInDashboard="true"
                @updateDashboard="$emit('updateDashboard')"
                @createDashboard="$emit('createDashboard')"
                @deleteDashboard="$emit('deleteDashboard')"
                @importDashboard="$emit('importDashboard')"
            ></backend-component-dashboard-dashboard-selector>

            <div v-if="store.state.editMode" class="dashboard-manage-controls">
                <div class="dashboard-button-set interval-selector">
                    <button class="dashboard-toolbar-button primary" :disabled="saving" @click.prevent="onApplyChanges"><?= e(trans('backend::lang.dashboard.apply_changes')) ?></button>
                    <button class="dashboard-toolbar-button" :disabled="saving" @click.prevent="onCancelChanges"><?= e(trans('backend::lang.form.cancel')) ?></button>
                </div>
            </div>
        </div>
        <backend-component-dashboard-interval-selector
            v-if="!store.state.editMode"
            :store="store"
        ></backend-component-dashboard-interval-selector>

    </div>

    <backend-component-dashboard-report
        :rows="currentDashboard.rows"
        :store=store
    ></backend-component-dashboard-report>
</div>