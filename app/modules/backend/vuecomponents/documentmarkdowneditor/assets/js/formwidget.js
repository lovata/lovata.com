oc.Modules.register('backend.vuecomponents.documentmarkdowneditor.formwidget', function() {
    'use strict';

    class FormWidget {
        constructor(element, options, changeCallback) {
            const widgetConnectorClass = Vue.extend(
                Vue.options.components['backend-component-documentmarkdowneditor-formwidgetconnector']
            );

            this.element = element;

            this.connectorInstance = new widgetConnectorClass({
                propsData: {
                    textarea: element,
                    useMediaManager: options.useMediaManager,
                    sideBySide: options.sideBySide,
                    options: options,
                    lang: $(element).closest('.field-markdowneditor').data()
                }
            });

            if (changeCallback) {
                this.connectorInstance.$on('change', function() {
                    changeCallback();
                });
            }

            this.element.addEventListener('change', this.onChangeTextarea);

            this.connectorInstance.$on('focus', function () {
                $(element).closest('.field-markdowneditor').addClass('editor-focus');
            });

            this.connectorInstance.$on('blur', function () {
                $(element).closest('.field-markdowneditor').removeClass('editor-focus');
            });

            this.connectorInstance.$mount();
            element.parentNode.appendChild(this.connectorInstance.$el);
        }

        onChangeTextarea = () => {
            this.setContent(this.element.value);
        }

        setContent(str) {
            if (this.connectorInstance) {
                this.connectorInstance.value = this.element.value;
            }
        }

        remove() {
            this.element.removeEventListener('change', this.onChangeTextarea);

            if (this.connectorInstance) {
                this.connectorInstance.$destroy();
                $(this.connectorInstance.$el).remove();
            }

            this.connectorInstance = null;
            this.element = null;
        }
    }

    return FormWidget;
});
