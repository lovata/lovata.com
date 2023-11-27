oc.Modules.register('backend.vuecomponents.richeditordocumentconnector.formwidget', function() {
    'use strict';

    class FormWidget {
        constructor(element, options, changeCallback) {
            const widgetConnectorClass = Vue.extend(
                Vue.options.components['backend-component-richeditor-document-connector-formwidgetconnector']
            );

            this.element = element;

            this.connectorInstance = new widgetConnectorClass({
                propsData: {
                    textarea: element,
                    useMediaManager: options.useMediaManager,
                    lang: $(element).closest('.field-richeditor').data(),
                    options: options
                }
            });

            if (changeCallback) {
                this.connectorInstance.$on('change', function() {
                    changeCallback();
                });
            }

            this.element.addEventListener('change', this.onChangeTextarea);

            this.connectorInstance.$on('focus', function() {
                $(element).closest('.editor-write').addClass('editor-focus');
            });

            this.connectorInstance.$on('blur', function() {
                $(element).closest('.editor-write').removeClass('editor-focus');
            });

            this.connectorInstance.$mount();
            element.parentNode.appendChild(this.connectorInstance.$el);
        }

        onChangeTextarea = () => {
            this.setContent(this.element.value);
        }

        getEditor() {
            if (this.connectorInstance) {
                return this.connectorInstance.getEditor();
            }
        }

        setContent(str) {
            if (this.connectorInstance) {
                this.connectorInstance.setContent(str);
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
