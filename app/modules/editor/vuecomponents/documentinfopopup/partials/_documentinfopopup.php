<backend-component-modal
    ref="modal"
    resizable="horizontal"
    size="small"
    unique-key="editor-info-modal"
    aria-labeled-by="editor-info-modal-title"
>
    <template v-slot:content>
        <div class="modal-header">
            <h4 class="modal-title" id="cms-info-modal-title" v-text="title"></h4>
            <button
                @click.prevent="$refs.modal.hide()"
                type="button"
                class="btn-close"
                aria-label="Close"
                tabindex="0"></button>
        </div>
        <div class="modal-body">
            <backend-component-infotable
                :items=items
            >
            </backend-component-infotable>
        </div>
        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-primary btn-default-action"
                @click.prevent="$refs.modal.hide()"
                data-default-focus
            ><?= e(trans('backend::lang.form.close')) ?></button>
        </div>
    </template>
</backend-component-modal>