oc.Modules.register('backend.component.documentmarkdowneditor.formwidgetconnector', function () {
    Vue.component('backend-component-documentmarkdowneditor-formwidgetconnector', {
        props: {
            textarea: null,
            lang: {},
            useMediaManager: {
                type: Boolean,
                default: false
            },
            sideBySide: {
                type: Boolean,
                default: true
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
                ]
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
            }
        },
        mounted: function onMounted() {
            this.value = this.textarea.value;

            Vue.nextTick(() => {
                this.$refs.markdownEditor.clearHistory();
            });
        },
        methods: {
            onFocus: function onFocus() {
                this.$emit('focus');
            },

            onBlur: function onBlur() {
                this.$emit('blur');
            },

            onToolbarCommand: function onToolbarCommand(cmd) {
                if (cmd == 'document:toggleFullscreen') {
                    this.fullScreen = !this.fullScreen;

                    Vue.nextTick(() => {
                        this.$refs.markdownEditor.refresh();
                    });
                }
            }
        },
        beforeDestroy: function beforeDestroy() {
            if (this.$refs.toolbar) {
                this.$refs.toolbar.$destroy();
            }

            this.$refs.document.$destroy();
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
        template: '#backend_vuecomponents_documentmarkdowneditor_formwidgetconnector'
    });
});