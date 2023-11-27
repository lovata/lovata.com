oc.Modules.register('tailor.publishingcontrols.domtools', function () {
    'use strict';

    return {
        findFormField(fieldName) {
            let elements = document.getElementsByName(fieldName);
            if (elements.length === 0) {
                return null;
            }

            return elements.item(0);
        },

        findFormGroup(fieldName) {
            let field = this.findFormField(fieldName);
            if (field === null) {
                return null;
            }

            return $(field).closest('.form-group');
        }
    };
});