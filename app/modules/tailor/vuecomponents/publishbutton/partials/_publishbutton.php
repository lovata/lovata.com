<button
    type="button"
    class="record-management-button has-menu record-status-button"
    :class="buttonCssClass"
    @click.prevent="onClick($event)"
>
    <i :style="markerStyle"></i>
    <span><span v-text="currentStatusName"></span> <em v-if="state.publishingStateChanged && !state.initial.isDraft"><?= __('Unsaved') ?></em></span>
</button>
