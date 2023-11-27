<?php namespace Tailor\Classes\EditorExtension;

use Lang;
use Tailor\Classes\Blueprint;
use Tailor\Classes\EditorExtension;
use Backend\VueComponents\TreeView\NodeDefinition;
use Editor\Classes\NewDocumentDescription;

/**
 * HasExtensionState initializes state for the Tailor Editor Extension
 */
trait HasExtensionState
{
    /**
     * getCmsPageNewDocumentData
     */
    private function getTailorBlueprintNewDocumentData()
    {
        $description = new NewDocumentDescription(
            trans('tailor::lang.blueprint.new'),
            $this->makeMetadataForNewTemplate(EditorExtension::DOCUMENT_TYPE_BLUEPRINT)
        );

        $description->setIcon(EditorExtension::ICON_COLOR_BLUEPRINT, 'backend-icon-background entity-small tailor-blueprint');
        $description->setInitialDocumentData([
            'fileName' => 'new-blueprint.yaml',
            'content' => $this->getBlueprintTemplate('entry')
        ]);

        return $description;
    }

    protected function addBlueprintsNavigatorNodes($rootNode)
    {
        $blueprintsNode = $rootNode->addNode(Lang::get('tailor::lang.blueprint.editor_node_name'), EditorExtension::DOCUMENT_TYPE_BLUEPRINT);
        $blueprintsNode
            ->setSortBy('isFolder:desc,fileName')
            ->setDragAndDropMode([NodeDefinition::DND_MOVE, NodeDefinition::DND_CUSTOM_EXTERNAL])
            ->setDisplayMode(NodeDefinition::DISPLAY_MODE_TREE)
            ->setChildKeyPrefix(EditorExtension::DOCUMENT_TYPE_BLUEPRINT.':')
            ->setMultiSelect(true)
            ->setHasApiMenuItems(true)
            ->setUserData([
                'path' => '/',
                'topLevel' => true
            ])
        ;

        $this->addDirectoryBlueprintNodes('', $blueprintsNode);
    }

    protected function addDirectoryBlueprintNodes(string $path, $parentNode)
    {
        $blueprints = (new Blueprint())->get([
            'recursive' => false,
            'filterPath' => $path
        ]);

        foreach ($blueprints as $blueprint) {
            $node = $parentNode
                ->addNode($blueprint['fileName'], $blueprint['path'])
                ->setHasApiMenuItems(true)
                ->setUserData($blueprint)
            ;

            if ($blueprint['isFolder']) {
                $node->setFolderIcon();
                $innerPath = $path ? $path.'/'.$blueprint['fileName'] : $blueprint['fileName'];
                $this->addDirectoryBlueprintNodes($innerPath, $node);
            }
            else {
                $node->setHideInQuickAccess(!$blueprint['isEditable']);
                $node->setNoMoveDrop(true);
                $node->setIcon(EditorExtension::ICON_COLOR_BLUEPRINT, 'backend-icon-background entity-small tailor-blueprint');
            }
        }
    }
}
