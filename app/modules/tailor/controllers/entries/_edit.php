<?php if (!$this->fatalError): ?>
    <?= Form::open(['id' => 'tailor-form', 'class' => 'layout stretch', 'data-change-monitor' => true]) ?>
        <div class="layout-row">
            <div class="padded-container layout form-document-layout">
                <div class="layout-row min-size">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <?= $this->formRender([
                                'section' => 'outside',
                                'preview' => $initialState['isDeleted']
                            ]) ?>
                        </div>
                        <div data-vue-container="entryHeaderControls">
                            <div class="record-management-controls">
                                <?php if ($initialState['showEntryTypeSelector']): ?>
                                    <backend-component-dropdownmenubutton
                                        :menuitems="state.entryTypeOptions"
                                        preferable-menu-position="bottom-right"
                                        css-class="record-management-button has-menu entry-type-selector"
                                        :current-label-command="state.initial.contentGroup"
                                        :disabled="state.toolbarDisabled"
                                        @command="onSetEntryType"
                                    ></backend-component-dropdownmenubutton>
                                <?php endif ?>

                                <tailor-component-publishbutton
                                    @click="onPublishingControlsBtnClick"
                                    :state="state"
                                ></tailor-component-publishbutton>
                            </div>

                            <tailor-component-publishingcontrols
                                :lang="state.lang"
                                :entry-state="state"
                                ref="publishingControls"
                                @statechanged="onPublishingStateChanged"
                                @publishdraftclick="onPublishDraftClick"
                                @restorerecordclick="onRestoreRecordClick"
                            ></tailor-component-publishingcontrols>
                        </div>
                    </div>
                </div>

                <div
                    class="layout-row min-size"
                    data-vue-container
                    data-lang-form-save="<?= e(Lang::get('backend::lang.form.save')) ?>"
                    data-lang-form-delete="<?= e(Lang::get('backend::lang.form.delete')) ?>"
                    data-lang-form-restore="<?= e(Lang::get('backend::lang.form.restore')) ?>"
                    data-lang-form-error="<?= e(Lang::get('backend::lang.form.error')) ?>"
                    data-lang-form-confirm-delete="<?= e(Lang::get('backend::lang.form.confirm_delete')) ?>"
                    data-lang-form-save-close="<?= e(Lang::get('backend::lang.form.save_and_close')) ?>"
                    data-lang-force-delete="<?= __('Delete Forever') ?>"
                    data-lang-force-delete-confirm="<?= __('Do you really want to delete this permanently?') ?>"
                    data-lang-save-draft="<?= __('Save Draft') ?>"
                    data-lang-discard-draft="<?= __('Discard Draft') ?>"
                    data-lang-discard-draft-confirm="<?= __('Do you really want to discard the draft?') ?>"
                    data-lang-save-apply-draft="<?= __('Save & Apply Draft') ?>"
                    data-lang-delete-entry-confirm="<?= __('Do you really want to delete the record? It will also delete all drafts if any exist.') ?>"
                    data-lang-create-draft="<?= __('Create New Draft') ?>"
                    data-lang-select-draft="<?= __('Select Draft to Edit') ?>"
                    data-lang-edit-primary-record="<?= __('Edit the Primary Record') ?>"
                    data-lang-unnamed-draft="<?= __('Unnamed draft') ?>"
                    data-lang-draft-notes="<?= __('Notes') ?>"
                    data-lang-confirm-create-draft="<?= __('The document has unsaved changes. Do you want to discard them and proceed with creating a new draft?') ?>"
                    data-lang-preview="<?= __('Preview') ?>"
                >
                    <div class="compensate-container-padding-h">
                        <template
                        >
                            <backend-component-document
                                :processing="state.processing"
                                :toolbar-command-event-bus="state.eventBus"
                            >
                                <template v-slot:toolbar>
                                    <backend-component-document-toolbar
                                        :elements="state.toolbarElements"
                                        @command="onCommand"
                                        :disabled="state.toolbarDisabled"
                                    ></backend-component-document-toolbar>
                                </template>
                                <template v-slot:drawer>
                                    <tailor-component-draftnotes
                                        v-if="state.showDraftNotesUI"
                                        v-show="state.showDraftNotes"
                                        ref="draftNotes"
                                        :state="state"
                                    >
                                    </tailor-component-draftnotes>
                                </template>
                            </backend-component-document>
                        </template>
                    </div>

                    <div style="display:none">
                        <?php if (!$initialState['isDeleted']): ?>
                            <?= $this->formRenderSecondaryTabs() ?>
                        <?php endif ?>
                    </div>
                </div>

                <div class="layout-row" id="entryPrimaryTabs">
                    <?= $this->makePartial('primary_tabs') ?>
                </div>
            </div>
        </div>
    <?= Form::close() ?>

    <script data-vue-state="initial" type="text/template"><?= json_encode($initialState) ?></script>
<?php else: ?>
    <div class="padded-container">
        <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>

        <p><?= Ui::button()->label('Return to Entries')->linkTo('tailor/entries') ?></p>
    </div>
<?php endif ?>
