<div
    class="dashboard-report-widget"
    :draggable="editMode"
    data-report-widget
    :class="cssClass"
    @dragstart.stop="onDragStart"
    @drop.stop="onDrop"
    @click="onClick"
    @dragover.stop="onDragOver"
    data-lang-prop-data-source="<?= e(trans('backend::lang.dashboard.widget_data_source')) ?>"
    data-lang-prop-dimension="<?= e(trans('backend::lang.dashboard.widget_dimension')) ?>"
    data-lang-prop-data-source-required="<?= e(trans('backend::lang.dashboard.widget_data_source_required')) ?>"
    data-lang-prop-dimension-required="<?= e(trans('backend::lang.dashboard.widget_dimension_required')) ?>"
    data-lang-prop-metric-required="<?= e(trans('backend::lang.dashboard.widget_metric_required')) ?>"
    data-lang-prop-metric="<?= e(trans('backend::lang.dashboard.widget_metric')) ?>"
    data-lang-prop-metrics="<?= e(trans('backend::lang.dashboard.widget_metrics')) ?>"
    data-lang-prop-title="<?= e(trans('backend::lang.dashboard.widget_title')) ?>"
    data-lang-prop-title-required="<?= e(trans('backend::lang.dashboard.widget_title_required')) ?>"
    data-lang-prop-title-optional-placeholder="<?= e(trans('backend::lang.dashboard.widget_title_optional_placeholder')) ?>"
    data-lang-prop-sort-by="<?= e(trans('backend::lang.dashboard.sort_by')) ?>"
    data-lang-sort-by-placeholder="<?= e(trans('backend::lang.dashboard.sort_by_placeholder')) ?>"
    data-lang-sort-by-required="<?= e(trans('backend::lang.dashboard.sort_by_required')) ?>"
    data-lang-sort-by-dimension="<?= e(trans('backend::lang.dashboard.sort_by_dimension')) ?>"
    data-lang-sort-by-metric="<?= e(trans('backend::lang.dashboard.sort_by_metric')) ?>"
    data-lang-prop-sort-order="<?= e(trans('backend::lang.dashboard.sort_order')) ?>"
    data-lang-sort-asc="<?= e(trans('backend::lang.dashboard.sort_asc')) ?>"
    data-lang-sort-desc="<?= e(trans('backend::lang.dashboard.sort_desc')) ?>"
    data-lang-group-sorting="<?= e(trans('backend::lang.dashboard.group_sorting')) ?>"
    data-lang-value-not-set="<?= e(trans('backend::lang.dashboard.value_not_set')) ?>"
    data-lang-prop-limit="<?= e(trans('backend::lang.dashboard.limit')) ?>"
    data-lang-prop-limit-number="<?= e(trans('backend::lang.dashboard.limit_number')) ?>"
    data-lang-prop-limit-placeholder="<?= e(trans('backend::lang.dashboard.limit_placeholder')) ?>"
    data-lang-prop-prop-limit-min="<?= e(trans('backend::lang.dashboard.limit_min')) ?>"
    data-lang-empty-values="<?= e(trans('backend::lang.dashboard.empty_values')) ?>"
    data-lang-empty-values-hide="<?= e(trans('backend::lang.dashboard.empty_values_hide')) ?>"
    data-lang-empty-values-display-not-set="<?= e(trans('backend::lang.dashboard.empty_values_display_not_set')) ?>"
    data-lang-prop-empty-dimension="<?= e(trans('backend::lang.dashboard.empty_values_dimension')) ?>"
    data-lang-date-interval="<?= e(trans('backend::lang.dashboard.date_interval')) ?>"
    data-lang-prop-date-interval="<?= e(trans('backend::lang.dashboard.prop_date_interval')) ?>"
    data-lang-date-interval-dashboard-default="<?= e(trans('backend::lang.dashboard.date_interval_dashboard_default')) ?>"
    data-lang-date-interval-this-quarter="<?= e(trans('backend::lang.dashboard.date_interval_this_quarter')) ?>"
    data-lang-date-interval-this-month="<?= e(trans('backend::lang.dashboard.date_interval_this_month')) ?>"
    data-lang-date-interval-this-week="<?= e(trans('backend::lang.dashboard.date_interval_this_week')) ?>"
    data-lang-date-interval-this-year="<?= e(trans('backend::lang.dashboard.date_interval_this_year')) ?>"
    data-lang-date-interval-past-days="<?= e(trans('backend::lang.dashboard.date_interval_past_days')) ?>"
    data-lang-date-interval-past-hour="<?= e(trans('backend::lang.dashboard.date_interval_past_hour')) ?>"
    data-lang-prop-past-days-value="<?= e(trans('backend::lang.dashboard.date_interval_past_days_value')) ?>"
    data-lang-prop-auto-update="<?= e(trans('backend::lang.dashboard.auto_update')) ?>"
    data-lang-date-interval-past-days-invalid="<?= e(trans('backend::lang.dashboard.date_interval_past_days_invalid')) ?>"
    data-lang-date-interval-past-days-placeholder="<?= e(trans('backend::lang.dashboard.date_interval_past_days_placeholder')) ?>"
    data-lang-prop-color="<?= e(trans('backend::lang.dashboard.prop_color')) ?>"
    data-lang-color-required="<?= e(trans('backend::lang.dashboard.color_required')) ?>"
    data-lang-tab-general="<?= e(trans('backend::lang.dashboard.tab_general')) ?>"
    data-lang-tab-sorting-filtering="<?= e(trans('backend::lang.dashboard.tab_sorting_filtering')) ?>"
    data-lang-prop-records-per-page="<?= e(trans('backend::lang.dashboard.prop_records_per_page')) ?>"
    data-lang-records-per-page-placeholder="<?= e(trans('backend::lang.dashboard.records_per_page_placeholder')) ?>"
    data-lang-records-per-page-invalid="<?= e(trans('backend::lang.dashboard.records_per_page_invalid')) ?>"
    data-lang-prop-display-totals="<?= e(trans('backend::lang.dashboard.prop_display_totals')) ?>"
    data-lang-filter-operation-equal-to="<?= e(trans('backend::lang.dashboard.filter_operation_equal_to')) ?>"
    data-lang-filter-operation-greater-equal="<?= e(trans('backend::lang.dashboard.filter_operation_greater_equal')) ?>"
    data-lang-filter-operation-less-equal="<?= e(trans('backend::lang.dashboard.filter_operation_less_equal')) ?>"
    data-lang-filter-operation-greater="<?= e(trans('backend::lang.dashboard.filter_operation_greater')) ?>"
    data-lang-filter-operation-less="<?= e(trans('backend::lang.dashboard.filter_operation_less')) ?>"
    data-lang-filter-operation-starts-with="<?= e(trans('backend::lang.dashboard.filter_operation_starts_with')) ?>"
    data-lang-filter-operation-includes="<?= e(trans('backend::lang.dashboard.filter_operation_includes')) ?>"
    data-lang-filter-operation-one-of="<?= e(trans('backend::lang.dashboard.filter_operation_one_of')) ?>"
    data-lang-prop-operation="<?= e(trans('backend::lang.dashboard.prop_operation')) ?>"
    data-lang-prop-value="<?= e(trans('backend::lang.dashboard.prop_value')) ?>"
    data-lang-prop-values="<?= e(trans('backend::lang.dashboard.prop_values')) ?>"
    data-lang-prop-values-one-per-line="<?= e(trans('backend::lang.dashboard.prop_values_one_per_line')) ?>"
    data-lang-prop-filter-attribute="<?= e(trans('backend::lang.dashboard.prop_filter_attribute')) ?>"
    data-lang-filter-select-attribute="<?= e(trans('backend::lang.dashboard.filter_select_attribute')) ?>"
    data-lang-filter-select-operation="<?= e(trans('backend::lang.dashboard.filter_select_operation')) ?>"
    data-lang-filter-enter-value="<?= e(trans('backend::lang.dashboard.filter_enter_value')) ?>"
    data-lang-prop-filters="<?= e(trans('backend::lang.dashboard.prop_filters')) ?>"
    data-lang-configure="<?= e(trans('backend::lang.dashboard.configure')) ?>"
    data-lang-apply="<?= e(trans('backend::lang.form.apply')) ?>"
    data-lang-delete="<?= e(trans('backend::lang.form.delete')) ?>"
>
    <div v-if="editMode && !isFrameless" class="resize-handle" @dragstart.stop.prevent="" @mousedown.stop.prevent="onHandleMouseDown" ref="resizeHandle"></div>

    <div v-show="editMode" class="widget-controls" @dragstart.stop.prevent="">
        <div
            class="edit-widget-button"
            role="button"
            tabindex="0"
            aria-label="Edit widget"
            @click.stop="onContextMenu($event)"
            @keyup.enter="onContextMenu($event)"
        >
            <img src="<?= Url::asset('/modules/backend/assets/images/dashboard/edit-dots.svg') ?>"/>
        </div>

        <backend-component-dropdownmenu
            :items="menuItems"
            ref="menu"
            @command="onMenuItemCommand"
        ></backend-component-dropdownmenu>
    </div>

    <div class="widget-inner-container" @dragstart.stop.prevent="">
        <div
            class="widget-size-container"
        >
            <backend-component-dashboard-widget-static
                v-if="widget.configuration.type == 'static'"
                :widget="widget"
                :store="store"
                :loading="loading"
                :autoUpdating="autoUpdating"
                :error="error"
                ref="widgetImplementation"
                @configure="showInspector"
                @reload="load"
            ></backend-component-dashboard-widget-static>
            <backend-component-dashboard-widget-indicator
                v-if="widget.configuration.type == 'indicator'"
                :widget="widget"
                :store="store"
                :loading="loading"
                :autoUpdating="autoUpdating"
                :error="error"
                ref="widgetImplementation"
                @configure="showInspector"
                @reload="load"
            ></backend-component-dashboard-widget-indicator>
            <backend-component-dashboard-widget-chart
                v-if="widget.configuration.type == 'chart'"
                :widget="widget"
                :store="store"
                :loading="loading"
                :autoUpdating="autoUpdating"
                :error="error"
                ref="widgetImplementation"
                @configure="showInspector"
                @reload="load"
            ></backend-component-dashboard-widget-chart>
            <backend-component-dashboard-widget-table
                v-if="widget.configuration.type == 'table'"
                :widget="widget"
                :store="store"
                :loading="loading"
                :autoUpdating="autoUpdating"
                :error="error"
                ref="widgetImplementation"
                @configure="showInspector"
                @reload="load"
            ></backend-component-dashboard-widget-table>
            <backend-component-dashboard-widget-sectiontitle
                v-if="widget.configuration.type == 'section-title'"
                :widget="widget"
                :store="store"
                :loading="loading"
                :error="error"
                ref="widgetImplementation"
            ></backend-component-dashboard-widget-sectiontitle>
            <backend-component-dashboard-widget-textnotice
                v-if="widget.configuration.type == 'notice'"
                :widget="widget"
                :store="store"
                :loading="loading"
                :error="error"
                ref="widgetImplementation"
            ></backend-component-dashboard-widget-textnotice>
            <template v-if="!isKnownWidgetType(widget.configuration.type)">
                <div v-if="!isComponentRegistered(widget.configuration.type)">
                    <div class="generic-widget-error">
                        Component not found: <span v-text="widget.configuration.type"></span>
                    </div>
                </div>
                <component
                    v-else
                    :is="widget.configuration.type"
                    :widget="widget"
                    :store="store"
                    :loading="loading"
                    :autoUpdating="autoUpdating"
                    :error="error"
                    ref="widgetImplementation"
                    @configure="showInspector"
                    @reload="load"
                ></component>
            </template>
        </div>
    </div>
</div>