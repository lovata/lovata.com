oc.Modules.register('tailor.app.base', function () {
    'use strict';

    const VueApp = oc.Modules.import('backend.october.vueapp');

    class TailorAppBase extends VueApp {
        constructor() {
            super();

            this.state.toolbarDisabled = false;
            this.state.toolbarExtensionPoint = [];
            this.state.toolbarExtraButtons = [];
            this.state.eventBus = new Vue();
            this.state.publishingStateChanged = false;
            this.state.toolbarElements = [];
        }

        initListeners() {
            // Listen for Turbo
            addEventListener('before-render', this.proxy(this.refreshToolbars));

            // Listen for AJAX
            $(window).on('oc.updateUi', this.proxy(this.refreshToolbars));

            // Extra check to fix a race condition
            $(window).one('shown.bs.tab', this.proxy(this.refreshToolbars));

            this.state.eventBus.$on('documentloadingstart', () => {
                this.state.processing = true;
            });

            this.state.eventBus.$on('documentloadingend', () => {
                this.state.processing = false;
            });
        }

        destroyListeners() {
            removeEventListener('before-render', this.proxy(this.refreshToolbars));
            $(window).off('oc.updateUi', this.proxy(this.refreshToolbars));
            $(window).off('shown.bs.tab', this.proxy(this.refreshToolbars));
            this.state.publishingStateChanged = false;
        }

        registerMethods() {
            super.registerMethods();
            this.registerMethod('onPublishingControlsBtnClick', ev => this.onPublishingControlsBtnClick(ev));
            this.registerMethod('onPublishingStateChanged', changed => this.onPublishingStateChanged(changed));
        }

        refreshToolbars() {
            this.state.toolbarExtensionPoint.splice(0);
            this.state.eventBus.$emit('extendapptoolbar');
        }

        refreshToolbarExtraButtons() {
            this.state.toolbarExtraButtons.splice(0);
        }

        handleFormSaved(data) {
            this.containers.entryHeaderControls.$refs.publishingControls.updateSavedState();
            $('#tailor-form').trigger('unchange.oc.changeMonitor');
        }

        onBeforeContainersInit() {
            this.refreshToolbarExtraButtons();
        }

        onAfterCommandSuccess(command, data) {
        }

        async onPreview(targetElement) {
            this.state.processing = true;
            this.state.toolbarDisabled = true;

            try {
                const data = await this.ajaxRequest(targetElement, 'onPreview', {});
                window.open(data.result);
            }
            catch (error) {}

            this.state.processing = false;
            this.state.toolbarDisabled = false;
        }

        async onCommand(command, isHotkey, ev, targetElement, customData, throwOnError) {
            this.state.toolbarDisabled = true;
            $('#tailor-form').trigger('pauseUnloadListener');

            try {
                let data = await super.onCommand(command, isHotkey, ev, targetElement, customData);
                this.state.toolbarDisabled = false;

                this.onAfterCommandSuccess(command, data);

                $('#tailor-form').trigger('resumeUnloadListener');
            }
            catch (error) {
                $('#tailor-form').trigger('resumeUnloadListener');
                this.state.toolbarDisabled = false;
                if (throwOnError) {
                    throw error;
                }
            }
        }

        onPublishingControlsBtnClick(ev) {
            this.containers.entryHeaderControls.$refs.publishingControls.show(ev.currentTarget);
        }

        onPublishingStateChanged(changed) {
            this.state.publishingStateChanged = changed;
        }
    }

    return TailorAppBase;
});