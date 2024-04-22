<div v-bind:tabindex="containerTabIndex" @focus="onContainerFocus">
    <backend-component-loading-indicator v-if="loadingDynamicOptions"
        size="tiny"
    ></backend-component-loading-indicator>

    <backend-component-dropdown
        v-if="!loadingDynamicOptions"
        :options="options"
        :id="controlId"
        :placeholder="control.placeholder"
        :tabindex="0"
        :disabled="inspectorPreferences.readOnly"
        :allow-empty="true"
        track-by="code"
        label="label"
        ref="input"
        v-model="selectedValue"
        select-label=""
        selected-label=""
        deselect-label=""
        @input="updateValue"
        @open="onFocus"
        @close="onBlur"
        @hook:mounted="onDropdownMounted"
    >
        <span slot="noResult"><?= e(trans('backend::lang.form.no_options_found')) ?></span>
        <template v-if="useValuesAsIcons || useValuesAsColors" slot="option" slot-scope="props">
            <div class="option-with-icon" v-if="useValuesAsIcons">
                <div class="option-icon" :class="props.option.code"></div>
                <span>{{ props.option.label }}</span>
            </div>

            <div class="option-with-color" v-if="useValuesAsColors">
                <div class="option-color" :style="{'background-color': props.option.code}"></div>
                <span>{{ props.option.label }}</span>
            </div>
        </template>
    </backend-component-dropdown>
</div>