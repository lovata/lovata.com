/*
 * Vue Inspector implementation
 */
oc.Modules.register('backend.component.inspector', function () {
    Vue.component('backend-component-inspector', {
        props: {
            dataSchema: {
                type: Array,
                required: true
            },
            data: {
                type: Object,
                required: true
            },
            liveMode: {
                type: Boolean,
                default: false
            },
            uniqueId: {
                type: String,
                required: true
            },
            layoutUpdateData: {
                type: Object
            },
            inspectorClass: {
                type: String
            },
            readOnly: {
                type: Boolean,
                default: false
            }
        },
        data: function () {
            if (typeof this.data !== 'object') {
                throw new Error('Inspector data.obj must be an object');
            }

            return {
                liveObject: this.liveMode ? this.data.obj : $.oc.vueUtils.getCleanObject(this.data.obj),
                originalData: this.liveMode ? $.oc.vueUtils.getCleanObject(this.data.obj) : null,
                parentObject: this.data.parentObj ? this.data.parentObj : {}
            };
        },
        computed: {
            inspectorPreferences: function computeInspectorPreferences() {
                return {
                    readOnly: this.readOnly,
                    inspectorClass: this.inspectorClass
                };
            }
        },
        methods: {
            getCleanObject: function getCleanObject() {
                return $.oc.vueUtils.getCleanObject(this.liveObject);
            },

            applyChanges: function applyChanges() {
                $.oc.vueComponentHelpers.inspector.utils.deepCloneObject(this.getCleanObject(), this.data.obj);
            },

            revertChanges: function cancelChanges() {
                if (!this.liveMode) {
                    throw new Error('Changes can only be reverted in live mode.');
                }

                $.oc.vueComponentHelpers.inspector.utils.deepCloneObject(this.originalData, this.data.obj);
            },

            validate: function validate() {
                return this.$refs.panel.validate();
            },

            onModalShown: function onModalShown() {
                this.layoutUpdateData.modalShown++;
            }
        },
        created: function created() {
            var validationError = $.oc.vueComponentHelpers.inspector.utils.validateDataSchema(this.dataSchema);

            if (typeof validationError === 'string') {
                console.log(this.dataSchema);
                throw new Error(validationError);
            }
        },
        template: '#backend_vuecomponents_inspector'
    });
});