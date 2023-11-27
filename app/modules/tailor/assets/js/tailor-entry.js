oc.Modules.register('tailor.app', function () {
    'use strict';

    const TailorAppBase = oc.Modules.import('tailor.app.base');

    class TailorApp extends TailorAppBase {
        constructor() {
            super();

            this.state.showDraftNotes = true;
        }

        registerMethods() {
            super.registerMethods();
            this.registerMethod('onSetEntryType', (command, isHotkey, ev, targetElement) => this.onSetEntryType(command, targetElement));
            this.registerMethod('onPublishDraftClick', ev => this.onPublishDraftClick(ev));
            this.registerMethod('onRestoreRecordClick', ev => this.onRestoreRecordClick(ev));
        }

        loadInitialState() {
            super.loadInitialState();
            this.makeEntryTypeOptions();

            if (!this.isDraftable()) {
                this.state.showDraftNotes = false;
            }
        }

        refreshToolbarExtraButtons() {
            super.refreshToolbarExtraButtons();
            if (this.isDraftable() && this.state.initial.isDraft && !this.state.initial.isFirstDraft) {
                this.state.toolbarExtraButtons.push({
                    type: 'button',
                    icon: 'octo-icon-notes-edit',
                    label: this.getLangString('DraftNotes'),
                    pressed: this.state.showDraftNotes,
                    command: 'togglenotes'
                });
            }
        }

        isDraftable() {
            return this.state.initial.draftable;
        }

        makeEntryTypeOptions() {
            this.state.entryTypeOptions = [];

            if (!this.state.initial.entryTypeOptions) {
                return;
            }

            const options = this.state.initial.entryTypeOptions;
            const keys = Object.keys(options);

            keys.forEach(key => {
                this.state.entryTypeOptions.push({
                    type: 'radiobutton',
                    command: key,
                    checked: this.state.initial.contentGroup == key,
                    label: options[key]
                });
            })
        }

        makeSaveButton() {
            let saveButtonCommand = 'form:onSave';
            let saveButtonLabel = this.getLangString('FormSave');
            let showBadge = false;

            if (this.isDraftable() && this.state.initial.isDraft) {
                saveButtonCommand = 'form:onCommitDraft';
                saveButtonLabel = this.getLangString('SaveDraft');
            }

            // Notify user about pending drafts
            if (this.state.initial.drafts.length && this.state.initial.canPublish) {
                showBadge = true;
            }

            // Only allow non-publishers to create drafts
            if (!this.state.initial.canPublish && !this.state.initial.isDraft) {
                saveButtonCommand = 'onCreateDraft';
                saveButtonLabel = this.getLangString('CreateDraft');
            }

            return {
                type: 'button',
                icon: 'octo-icon-save',
                label: saveButtonLabel,
                tooltip: saveButtonLabel,
                hotkey: 'ctrl+s, cmd+s',
                tooltipHotkey: '⌃S, ⌘S',
                command: saveButtonCommand,
                showBadge: showBadge,
                menuitems: this.makeSaveMenuItems(),
                customData: {
                    request: {
                        data: { 'redirect': 0 }
                    }
                }
            }
        }

        makeSaveMenuItems() {
            if (!this.isDraftable() || !this.state.initial.canPublish) {
                return null;
            }

            let result = [];

            if (this.state.initial.isDraft) {
                result = [
                    {
                        type: 'text',
                        label: this.getLangString('SaveApplyDraft'),
                        command: 'form:onPublishDraft'
                    }
                ];
            }

            if (this.state.initial.isDraft && !this.state.initial.isFirstDraft) {
                result.push({
                    type: 'text',
                    href: this.state.initial.primaryRecordUrl,
                    label: this.getLangString('EditPrimaryRecord')
                });
            }

            if (!this.state.initial.isFirstDraft) {
                if (result.length > 0) {
                    result.push({ type: 'separator' });
                }
                result.push({
                    type: 'text',
                    label: this.getLangString('CreateDraft'),
                    command: 'onCreateDraft'
                });
            }

            if (this.state.initial.drafts.length) {
                const draftItems = [];
                const draftItemType = this.state.initial.isDraft && !this.state.initial.isFirstDraft ? 'radiobutton' : 'text';

                this.state.initial.drafts.forEach(draft => {
                    draftItems.push({
                        type: draftItemType,
                        href: this.state.initial.primaryRecordUrl + '?draft=' + draft.id,
                        label: draft.primary_attrs.name,
                        checked: this.state.initial.currentDraftId == draft.id,
                        disabled: this.state.initial.currentDraftId == draft.id
                    })
                });

                result.push(
                    {
                        type: 'text',
                        label: this.getLangString('SelectDraft'),
                        items: draftItems
                    }
                );
            }

            return result;
        }

        makeDeleteButton() {
            let deleteButtonLabel = this.getLangString('FormDelete');
            let deleteButtonCmd = 'form:onDelete';
            let deleteButtonConfirm = this.getLangString('DeleteEntryConfirm');
            if (this.state.initial.isDraft) {
                deleteButtonLabel = this.getLangString('DiscardDraft');
                deleteButtonCmd = this.state.initial.isFirstDraft
                    ? 'form:onForceDelete'
                    : 'form:onDiscardDraft';

                deleteButtonConfirm = this.getLangString('DiscardDraftConfirm');
            }
            else if (this.state.initial.isDeleted) {
                deleteButtonLabel = this.getLangString('ForceDelete');
                deleteButtonCmd = 'form:onForceDelete';
                deleteButtonConfirm = this.getLangString('ForceDeleteConfirm');
            }

            return {
                type: 'button',
                icon: 'octo-icon-delete',
                command: deleteButtonCmd,
                hotkey: 'shift+option+d',
                label: deleteButtonLabel,
                fixedRight: true,
                tooltip: deleteButtonLabel,
                tooltipHotkey: '⇧⌥D',
                customData: {
                    confirm: deleteButtonConfirm
                }
            };
        }

        handleFormSaved(data) {
            super.handleFormSaved(data);
            this.state.initial.statusCode = data.result.statusCode;
            this.state.initial.drafts = data.result.drafts;
            this.state.initial.fullSlug = data.result.fullSlug;
            Vue.set(this.state.toolbarElements, 0, this.makeSaveButton());
        }

        loadShowDraftNotes() {
            if (!this.isDraftable()) {
                return false;
            }

            let lastDraftId = localStorage.getItem('tailor-display-notes-last-draft-id');
            if (parseInt(lastDraftId) < this.state.initial.currentDraftId) {
                return true;
            }


            let item = localStorage.getItem('tailor-draft-display-notes');
            if (item === null) {
                return true;
            }

            return parseInt(item) == 1;
        }

        saveShowDraftNotes() {
            localStorage.setItem('tailor-draft-display-notes', this.state.showDraftNotes ? 1 : 0);
            localStorage.setItem('tailor-display-notes-last-draft-id', this.state.initial.currentDraftId);
        }

        onBeforeContainersInit() {
            this.state.showDraftNotesUI = this.state.initial.isDraft && !this.state.initial.isFirstDraft && this.isDraftable();
            if (this.state.showDraftNotesUI) {
                this.state.showDraftNotes = this.loadShowDraftNotes();
            }

            // Build Toolbar
            this.state.toolbarElements = [];

            // Save
            if (this.state.initial.canRestore) {
                    this.state.toolbarElements.push({
                        type: 'button',
                        icon: 'octo-icon-refresh',
                        label: this.getLangString('FormRestore'),
                        tooltip: this.getLangString('FormRestore'),
                        hotkey: 'ctrl+enter, cmd+enter',
                        tooltipHotkey: '⌃S, ⌘S',
                        command: 'form:onRestore'
                    });
            }
            else {
                this.state.toolbarElements.push(this.makeSaveButton());

                // Save and Close
                if (!this.state.initial.isSingular && (this.state.initial.canPublish || this.state.initial.isDraft)) {
                    this.state.toolbarElements.push({
                        type: 'button',
                        icon: 'octo-icon-keyboard-return',
                        label: this.getLangString('FormSaveClose'),
                        tooltip: this.getLangString('FormSaveClose'),
                        hotkey: 'ctrl+enter, cmd+enter',
                        tooltipHotkey: '⌃S, ⌘S',
                        command: this.state.initial.isDraft ? 'form:onCommitDraft' : 'form:onSave',
                        customData: {
                            request: {
                                data: { 'close': 1 }
                            }
                        }
                    });
                }
            }

            // Extras
            this.state.toolbarElements.push(this.state.toolbarExtraButtons);

            // Delete
            if (this.state.initial.canDelete && !this.state.initial.isCreateAction) {
                this.state.toolbarElements.push(this.makeDeleteButton());
            }

            // Preview
            if (this.state.initial.hasPreviewPage && !this.state.initial.isCreateAction) {
                this.state.toolbarElements.push({
                    type: 'button',
                    icon: 'octo-icon-location-target',
                    command: 'onPreview',
                    label: this.getLangString('Preview')
                });
            }

            // Extensions
            this.state.toolbarElements.push(this.state.toolbarExtensionPoint);

            super.onBeforeContainersInit();
        }

        async onCreateDraft(command, isHotkey, ev, targetElement, customData, throwOnError) {
            if (!$('#tailor-form').hasClass('oc-data-changed')) {
                this.onCommand('form:onCreateDraft', isHotkey, ev, targetElement, customData, throwOnError);
                return;
            }

            try {
                await $.oc.confirmPromise(this.getLangString('ConfirmCreateDraft'));

                this.onCommand('form:onCreateDraft', isHotkey, ev, targetElement, customData, throwOnError);
            } catch (error) {}
        }

        onAfterCommandSuccess(command, data) {
            if (command == 'form:onSave' || command == 'form:onCommitDraft') {
                this.handleFormSaved(data);
            }
        }

        async onCommand(command, isHotkey, ev, targetElement, customData, throwOnError) {
            if (command === 'togglenotes') {
                this.state.showDraftNotes = !this.state.showDraftNotes;
                this.saveShowDraftNotes();
                this.refreshToolbarExtraButtons();
                return;
            }

            if (command === 'onCreateDraft') {
                this.onCreateDraft(command, isHotkey, ev, targetElement, customData, throwOnError);
                return;
            }

            if (command === 'onPreview') {
                this.onPreview(targetElement);
                return;
            }

            if (!super.isFormCommand(command)) {
                return;
            }

            await super.onCommand(command, isHotkey, ev, targetElement, customData, throwOnError);
        }

        async onSetEntryType(entryType, targetElement) {
            this.state.processing = true;
            this.state.toolbarDisabled = true;

            try {
                await this.ajaxRequest(targetElement, 'onChangeEntryType', {
                    data: {
                        EntryRecord: {
                            content_group: entryType
                        }
                    }
                });

                this.state.initial.contentGroup = entryType;
                this.makeEntryTypeOptions();
            }
            catch (response) {
                $.oc.vueComponentHelpers.modalUtils.showAlert(this.getLangString('FormError'), response.responseText);
            }

            this.state.toolbarDisabled = false;
            this.state.processing = false;
        }

        async onPublishDraftClick(ev) {
            try {
                await this.onCommand('form:onPublishDraft', false, ev, ev.currentTarget, {}, true)
            }
            catch (error) {
                this.containers.entryHeaderControls.$refs.publishingControls.hide();
            }
        }

        async onRestoreRecordClick(ev) {
            try {
                await this.onCommand('form:onRestore', false, ev, ev.currentTarget, {}, true)
            }
            catch (error) {
                this.containers.entryHeaderControls.$refs.publishingControls.hide();
            }
        }
    }

    return new TailorApp();
});
