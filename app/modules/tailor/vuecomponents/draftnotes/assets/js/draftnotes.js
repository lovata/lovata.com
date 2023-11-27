/*
 * Tailor draft notes management Vue component
 */
Vue.component('tailor-component-draftnotes', {
    props: {
        state: Object
    },
    data: function() {
        return {
        };
    },
    computed: {
        draftName() {
            let draftNotes = this.state.initial.draftNotes;
            let initialDraftName = this.state.initial.draftName;

            if (!draftNotes || !draftNotes.length) {
                if (!initialDraftName || !initialDraftName.length) {
                    return this.state.lang['UnnamedDraft'];
                }

                return initialDraftName;
            }

            let parts = draftNotes.split(':');
            if (parts.length === 1) {
                let result = draftNotes.substring(0, 30);
                if (result.length < draftNotes.length) {
                    result += '...';
                }

                return result;
            }

            return parts[0];
        }
    },
    methods: {

    },
    mounted: function onMounted() {
    },
    watch: {
    },
    template: '#tailor_vuecomponents_draftnotes'
});