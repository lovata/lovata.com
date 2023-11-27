oc.Modules.register('tailor.editor.extension.documentcontroller.blueprint', function() {
    'use strict';

    const DocumentControllerBase = oc.Modules.import('editor.extension.documentcontroller.base');
    const EditorCommand = oc.Modules.import('editor.command');
    const FileSystemFunctions = oc.Modules.import('editor.extension.filesystemfunctions');

    class DocumentControllerBlueprint extends DocumentControllerBase {
        get documentType() {
            return 'tailor-blueprint';
        }

        get vueEditorComponentName() {
            return 'tailor-editor-component-blueprint-editor';
        }

        beforeDocumentOpen(commandObj, nodeData) {
            if (!nodeData.userData) {
                return false;
            }

            if (nodeData.userData.isFolder) {
                return false;
            }

            if (nodeData.userData.isEditable) {
                return true;
            }

            return false;
        }

        initListeners() {
            this.on('tailor:navigator-context-menu-display', this.getNavigatorContextMenuItems);
            this.on('tailor:tailor-blueprint-create-directory', this.onCreateDirectory);
            this.on('tailor:tailor-blueprint-delete', this.onDeleteBlueprintOrDirectory);
            this.on('tailor:tailor-blueprint-rename', this.onRenameBlueprintOrDirectory);
            this.on('tailor:navigator-node-moved', this.onNavigatorNodeMoved);
            this.on('tailor:tailor-blueprint-upload', this.onUploadDocument);
            this.on('tailor:navigator-external-drop', this.onNavigatorExternalDrop);
        }

        getNavigatorContextMenuItems(commandObj, payload) {
            const DocumentUri = oc.Modules.import('editor.documenturi');
            const uri = DocumentUri.parse(payload.nodeData.uniqueKey);
            const parentPath = payload.nodeData.userData.path;

            // TODO: handle tailor:root item for the Tailor section menus.

            if (uri.documentType !== this.documentType) {
                return;
            }

            const blueprintTypes = ['entry', 'single', 'stream', 'structure', 'mixin', 'global'];
            const blueprintItems = [];

            blueprintTypes.forEach(type => {
                blueprintItems.push({
                    label: this.trans('tailor::lang.blueprint.' + type),
                    type: 'text',
                    command: new EditorCommand('tailor:create-document@' + this.documentType, {
                        path: parentPath,
                        type
                    })
                });
            });

            if (payload.nodeData.userData.isFolder || payload.nodeData.userData.topLevel) {
                payload.menuItems.push({
                    type: 'text',
                    icon: 'octo-icon-create',
                    items: blueprintItems,
                    label: this.trans('tailor::lang.blueprint.new')
                });

                payload.menuItems.push({
                    type: 'text',
                    icon: 'octo-icon-upload',
                    command: new EditorCommand('tailor:tailor-blueprint-upload@' + this.documentType, {
                        path: parentPath
                    }),
                    label: this.trans('tailor::lang.blueprint.upload_files')
                });

                payload.menuItems.push({
                    type: 'text',
                    icon: 'octo-icon-folder',
                    command: 'tailor:tailor-blueprint-create-directory@' + parentPath,
                    label: this.trans('tailor::lang.blueprint.create_directory')
                });

                if (payload.nodeData.userData.topLevel) {
                    return;
                }

                payload.menuItems.push({
                    type: 'separator'
                });
            }

            payload.menuItems.push({
                type: 'text',
                icon: 'octo-icon-terminal',
                command: new EditorCommand('tailor:tailor-blueprint-rename@' + parentPath, {
                    fileName: payload.nodeData.userData.fileName
                }),
                label: this.trans('tailor::lang.blueprint.rename')
            });

            payload.menuItems.push({
                type: 'text',
                icon: 'octo-icon-delete',
                command: new EditorCommand('tailor:tailor-blueprint-delete@' + parentPath, {
                    itemsDetails: payload.itemsDetails
                }),
                label: this.trans('tailor::lang.blueprint.delete')
            });
        }

        preprocessNewDocumentData(newDocumentData, commandObj) {
            if (!commandObj.userData.type) {
                return;
            }

            const blueprintTemplates = this.parentExtension.state.customData.blueprintTemplates;
            const blueprintType = commandObj.userData.type;
            if (!blueprintTemplates[blueprintType]) {
                return;
            }

            newDocumentData.document.content = blueprintTemplates[blueprintType];
        }

        onBeforeDocumentCreated(commandObj, payload, documentData) {
            let parentPath = '';
            if (commandObj.userData && commandObj.userData.path) {
                parentPath = commandObj.userData.path;
            }

            if (parentPath.length > 0 && parentPath !== '/') {
                documentData.document.fileName = parentPath + '/' + documentData.document.fileName;
            }
        }

        onCreateDirectory(cmd, payload) {
            const fs = new FileSystemFunctions(this);

            fs.createDirectoryFromNavigatorMenu('onBlueprintCreateDirectory', cmd, payload)
        }

        onRenameBlueprintOrDirectory(cmd, payload) {
            const fs = new FileSystemFunctions(this);
            fs.renameFileOrDirectoryFromNavigatorMenu('onBlueprintRename', cmd, payload);
        }

        onUploadDocument(cmd) {
            const fs = new FileSystemFunctions(this);

            fs.uploadDocument(['.yaml'], 'onBlueprintUpload', cmd);
        }

        onNavigatorExternalDrop(cmd) {
            const fs = new FileSystemFunctions(this);

            fs.handleNavigatorExternalDrop('onBlueprintUpload', cmd);
        }

        async onDeleteBlueprintOrDirectory(cmd, payload) {
            const fs = new FileSystemFunctions(this);
            await fs.deleteFileOrDirectoryFromNavigatorMenu('onBlueprintDelete', cmd, payload);
        }

        async onNavigatorNodeMoved(cmd) {
            const fs = new FileSystemFunctions(this);
            await fs.handleNavigatorNodeMove('onBlueprintMove', cmd);
        }
    }

    return DocumentControllerBlueprint;
});
