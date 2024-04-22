<div
    class="report-row"
    :class="cssClass"
    data-report-row
    @drop.stop="onDrop"
    @dragover.stop="onDragOver"
    data-lang-item-custom="<?= e(trans('backend::lang.dashboard.menu_item_custom')) ?>"
    data-lang-item-delete-row="<?= e(trans('backend::lang.dashboard.menu_item_delete_row')) ?>"
>
    <div v-if="store.state.editMode" class="row-controls" @dragstart.stop.prevent="">
        <div
            class="edit-row-button"
            data-edit-row-button
            role="button"
            tabindex="0"
            aria-label="Edit row"
            @mousedown="onRowButtonMouseDown"
            @contextmenu.stop="onContextMenu($event)"
            @click.stop="onContextMenu($event)"
            v-on:keyup.enter="onContextMenu($event)"
        >
            <img src="<?= Url::asset('/modules/backend/assets/images/dashboard/edit-dots.svg') ?>"/>
        </div>

        <backend-component-dropdownmenu
            :items="menuItems"
            ref="menu"
            @command="onMenuItemCommand"
        ></backend-component-dropdownmenu>
    </div>

    <div class="row-widgets" >
        <div
            v-if="store.state.editMode && !hasWidgets"
            class="add-widget-button"
            @click.stop="onAddWidgetClick($event)"
        >
            <i class="ph ph-plus"></i>
            <span><?= e(trans('backend::lang.dashboard.add_widget')) ?></span>
        </div>

        <backend-component-dropdownmenu
            :items="addWidgetItems"
            ref="addWidgetMenu"
            @command="onAddWidgetMenuItemCommand"
        ></backend-component-dropdownmenu>

        <template v-for="(widget, index) in row.widgets">
            <backend-component-dashboard-report-widget
                :key="widget._unique_key"
                :widget="widget"
                :row="row"
                :rows="rows"
                :widget-index-in-row="index"
                :row-index="rowIndex"
                :store=store
            ></backend-component-dashboard-report-widget>
        </template>
    </div>
</div>