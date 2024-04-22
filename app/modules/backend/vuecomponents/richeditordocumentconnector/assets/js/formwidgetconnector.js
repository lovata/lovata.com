oc.Modules.register('backend.component.richeditor.document.connector.formwidgetconnector', function () {
    Vue.component('backend-component-richeditor-document-connector-formwidgetconnector', {
        props: {
            textarea: null,
            lang: {},
            useMediaManager: {
                type: Boolean,
                default: false
            },
            options: Object
        },
        data: function () {
            const toolbarExtensionPoint = [];

            return {
                toolbarExtensionPoint,
                fullScreen: false,
                value: ''
            };
        },
        computed: {
            toolbarElements: function computeToolbarElements() {
                return [
                    this.toolbarExtensionPoint,
                    {
                        type: 'button',
                        icon: this.fullScreen ? 'icon-fullscreen-collapse' : 'icon-fullscreen',
                        command: 'document:toggleFullscreen',
                        pressed: this.fullScreen,
                        fixedRight: true,
                        tooltip: this.lang.langFullscreen
                    }
                ];
            },

            editorOptions: function computeEditorOptions() {
                if (typeof this.options.editorOptions !== 'object') {
                    return {};
                }

                return this.options.editorOptions;
            },

            toolbarButtons: function computeToolbarButtons() {
                if (typeof this.options.toolbarButtons !== 'string') {
                    return [];
                }

                return this.options.toolbarButtons.split(',').map((button) => {
                    return button.trim();
                });
            },

            fullPage: function computeFullPage() {
                return !!this.options.fullpage;
            },

            readOnly: function computeReadOnly() {
                return this.options.readOnly;
            },

            externalToolbarAppState: function computeExternalToolbarAppState() {
                return this.options.externalToolbarAppState;
            },

            toolbarExtensionPointProxy: function computeToolbarExtensionPointProxy() {
                if (!this.options.externalToolbarAppState) {
                    return this.toolbarExtensionPoint;
                }

                const point = $.oc.vueUtils.getToolbarExtensionPoint(
                    this.options.externalToolbarAppState,
                    this.textarea
                );

                return point ? point.state : this.toolbarExtensionPoint;
            },

            hasExternalToolbar: function computeHasExternalToolbar() {
                return !!this.options.externalToolbarAppState;
            },

            showMargins: function computeShowMargins() {
                return this.options.showMargins ? true : false;
            }
        },
        mounted: function onMounted() {
            this.value = this.textarea.value;
        },
        methods: {
            getEditor: function getEditor() {
                return this.$refs.richeditor.getEditor();
            },

            setContent: function setContent(str) {
                this.value = str;
            },

            onToolbarCommand: function onToolbarCommand(cmd) {
                if (cmd == 'document:toggleFullscreen') {
                    this.fullScreen = !this.fullScreen;
                }
            },

            onFocus: function onFocus() {
                this.$emit('focus');
            },

            onBlur: function onBlur() {
                this.$emit('blur');
            }
        },
        beforeDestroy: function beforeDestroy() {
            this.textarea = null;
        },
        watch: {
            value: function watchValue(newValue) {
                if (newValue != this.textarea.value) {
                    this.textarea.value = newValue;
                    this.$emit('change');
                }
            }
        },
        template: '#backend_vuecomponents_richeditordocumentconnector_formwidgetconnector'
    });
});