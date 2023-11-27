<?php namespace Tailor\Models\GlobalRecord;

use Tailor\Classes\Blueprint\GlobalBlueprint;
use Tailor\Classes\BlueprintIndexer;
use DateTimeInterface;
use SystemException;

/**
 * HasGlobalBlueprint
 *
 * @property string $blueprint_uuid
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasGlobalBlueprint
{
    /**
     * @var GlobalBlueprint blueprintCache
     */
    protected $blueprintCache;

    /**
     * bootNestedTree constructor
     */
    public function initializeHasGlobalBlueprint()
    {
        $this->bindEvent('model.saveInternal', function() {
            $this->storeBlueprintContent();
        });

        $this->bindEvent('model.afterFetch', function() {
            $this->fetchBlueprintContent();
            $this->extendWithBlueprint();
        });

        $this->bindEvent('model.newInstance', function($model) {
            $model->extendWithBlueprint($this->blueprint_uuid);
        });
    }

    /**
     * getBlueprintDefinition
     */
    public function getBlueprintDefinition(): GlobalBlueprint
    {
        if ($this->blueprintCache !== null) {
            return $this->blueprintCache;
        }

        $uuid = $this->blueprint_uuid;
        if (!$uuid) {
            throw new SystemException('Missing global definition. Call GlobalRecord::inGlobal() to set one.');
        }

        $blueprint = BlueprintIndexer::instance()->findGlobal($uuid);
        if (!$blueprint) {
            throw new SystemException(sprintf('Unable to find global blueprint with ID "%s".', $uuid));
        }

        return $this->blueprintCache = $blueprint;
    }

    /**
     * fetchBlueprintContent
     */
    public function fetchBlueprintContent()
    {
        $content = $this->content;
        $contentColumns = $this->getFieldsetColumnNames();

        // Fetch content attributes
        foreach ($contentColumns as $key) {
            if (array_key_exists($key, $content)) {
                $this->$key = $content[$key];
            }
        }
    }

    /**
     * storeBlueprintContent
     */
    public function storeBlueprintContent()
    {
        $content = [];
        $contentColumns = $this->getFieldsetColumnNames();

        // Save attributes to content, purge from model
        $toSave = array_only($this->attributes, $contentColumns);
        foreach ($toSave as $key => $value) {
            if ($this->isJsonable($key)) {
                $content[$key] = $this->getAttribute($key);
            }
            elseif ($value instanceof DateTimeInterface) {
                $content[$key] = $value->format($this->getDateFormat());
            }
            elseif (is_bool($value)) {
                $content[$key] = (int) $value;
            }
            else {
                $content[$key] = $value;
            }

            unset($this->attributes[$key]);
        }

        $this->content = $content;
    }
}
