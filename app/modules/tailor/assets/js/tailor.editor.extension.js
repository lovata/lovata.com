oc.Modules.register('editor.extension.tailor.main', function() {
    'use strict';

    const ExtensionBase = oc.Modules.import('editor.extension.base');
    const DocumentUri = oc.Modules.import('editor.documenturi');
    const EditorCommand = oc.Modules.import('editor.command');

    class TailorEditorExtension extends ExtensionBase {
        constructor(namespace) {
            super(namespace);
        }

        listDocumentControllerClasses() {
            return [
                oc.Modules.import('tailor.editor.extension.documentcontroller.blueprint'),
            ];
        }

        removeFileExtension(fileName) {
            return fileName.split('.').slice(0, -1).join('.');
        }

        onCommand(commandString, payload) {
            super.onCommand(commandString, payload);

            if (commandString === 'tailor:refresh-navigator') {
                this.editorStore.refreshExtensionNavigatorNodes(this.editorNamespace).then(() => {});

                return;
            }
        }
    }

    return TailorEditorExtension;
});
