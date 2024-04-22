/*
 * Vue basic modal implementation
 */
oc.Modules.register('backend.component.modal.basic', function () {
    Vue.component('backend-component-modal-basic', {
        extends: Vue.options.components['backend-component-modal-alert'],
        props: {
            title: String
        },
        data: function () {
            return {
            };
        },
        computed: {
        },
        methods: {
            onCloseClick: function onButtonClick() {
                this.$refs.modal.hide();
            },

            onClick: function onClick(ev) {
                if (!ev.target.dataset.closePopup) {
                    return;
                }

               this.$emit('closeclick', ev.target.dataset.closePopup);
                this.$refs.modal.hide();
            }
        },
        template: '#backend_vuecomponents_modal_basic'
    });
});