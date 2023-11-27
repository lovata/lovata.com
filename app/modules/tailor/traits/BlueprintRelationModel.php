<?php namespace Tailor\Traits;

/**
 * BlueprintRelationModel adds blueprint support for relations
 *
 * Usage:
 *
 * In the model class definition add:
 *
 *   use \Tailor\Traits\BlueprintRelationModel;
 *
 *   public $belongsToMany = [..., 'blueprint' => '6947ff28-b660-47d7-9240-24ca6d58aeae'];
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait BlueprintRelationModel
{
    /**
     * @var array blueprintRelationDefinitions
     */
    protected $blueprintRelationDefinitions;

    /**
     * initializeBlueprintRelationModel trait for the model.
     */
    public function initializeBlueprintRelationModel()
    {
        $this->bindEvent('model.afterRelation', function ($relationName, $relationModel) {
            if (isset($this->blueprintRelationDefinitions[$relationName])) {
                $relationModel->extendWithBlueprint($this->blueprintRelationDefinitions[$relationName]);
            }
        });

        $this->defineBlueprintRelationModels();
    }

    /**
     * defineBlueprintRelationModels will spin over every relation and check for pivotSortable mode
     */
    protected function defineBlueprintRelationModels()
    {
        $supportsBlueprints = ['belongsToMany', 'belongsTo', 'hasMany', 'morphedByMany', 'morphToMany'];

        foreach ($supportsBlueprints as $type) {
            foreach ($this->$type as $name => $definition) {
                if (!isset($definition['blueprint'])) {
                    continue;
                }

                $this->blueprintRelationDefinitions[$name] = $definition['blueprint'];
            }
        }
    }
}
