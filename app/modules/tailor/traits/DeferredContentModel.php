<?php namespace Tailor\Traits;

use Tailor\Models\RepeaterItem;

/**
 * DeferredContentModel modifies deferred binding to support content UUIDs
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait DeferredContentModel
{
    /**
     * bootDeferredContentModel trait for a model.
     */
    public function initializeDeferredContentModel()
    {
        $this->bindEvent('deferredBinding.newBindInstance', function ($binding) {
            if ($this instanceof RepeaterItem) {
                $extraData = ['_contentSpawnPath' => $this->content_spawn_path];
            }
            else {
                $extraData = ['_contentUuid' => $this->blueprint_uuid];
            }

            $binding->pivot_data = $extraData + $binding->pivot_data;
        });
    }

    /**
     * extendDeferredContentModel
     */
    public function extendDeferredContentModel($model)
    {
        $pivotData = $model->pivot_data;

        if ($this instanceof RepeaterItem) {
            $this->extendWithBlueprintSpawn($pivotData['_contentSpawnPath']);
        }
        else {
            $this->extendWithBlueprint($pivotData['_contentUuid']);
        }
    }
}
