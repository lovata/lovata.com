<?php namespace Tailor\Models\RecordImport;

use Tailor\Models\EntryRecord;
use Tailor\Models\GlobalRecord;
use Tailor\Classes\Fieldset;
use Tailor\Classes\Blueprint;
use Tailor\Classes\BlueprintIndexer;
use Tailor\Classes\Blueprint\EntryBlueprint;
use Tailor\Classes\Blueprint\GlobalBlueprint;
use Tailor\Classes\Blueprint\StructureBlueprint;
use SystemException;

/**
 * HasGlobalBlueprint
 *
 * @property string $blueprint_uuid
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasGeneralBlueprint
{
    /**
     * @var Blueprint blueprintCache
     */
    protected $blueprintCache;

    /**
     * setBlueprintUuid
     */
    public function setBlueprintUuid($value)
    {
        $this->blueprint_uuid = $value;
    }

    /**
     * getBlueprintDefinition
     */
    public function getBlueprintDefinition(): Blueprint
    {
        if ($this->blueprintCache !== null) {
            return $this->blueprintCache;
        }

        $uuid = $this->blueprint_uuid;
        if (!$uuid) {
            throw new SystemException('Missing a blueprint definition in import/export model.');
        }

        $blueprint = BlueprintIndexer::instance()->find($uuid);
        if (!$blueprint) {
            throw new SystemException(sprintf('Unable to find import/export blueprint with ID "%s".', $uuid));
        }

        return $this->blueprintCache = $blueprint;
    }

    /**
     * getContentFieldsetDefinition
     */
    public function getContentFieldsetDefinition(): Fieldset
    {
        $fieldset = BlueprintIndexer::instance()->findContentFieldset($this->blueprint_uuid);

        if (!$fieldset) {
            throw new SystemException("Unable to find content fieldset definition with UUID of '{$this->blueprint_uuid}'.");
        }

        return $fieldset;
    }

    /**
     * isEntryStructure
     */
    public function isEntryStructure(): bool
    {
        return $this->getBlueprintDefinition() instanceof StructureBlueprint;
    }

    /**
     * resolveBlueprintModel
     */
    protected function resolveBlueprintModel()
    {
        $blueprint = $this->getBlueprintDefinition();

        if ($blueprint instanceof GlobalBlueprint) {
            return GlobalRecord::inGlobalUuid($this->blueprint_uuid);
        }

        if ($blueprint instanceof EntryBlueprint) {
            return EntryRecord::inSectionUuid($this->blueprint_uuid);
        }

        throw new SystemException('Unable to find a usable blueprint in import/export model.');
    }
}
