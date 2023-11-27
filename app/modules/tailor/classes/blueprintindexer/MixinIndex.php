<?php namespace Tailor\Classes\BlueprintIndexer;

use Tailor\Classes\Blueprint\MixinBlueprint;

/**
 * MixinIndex
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait MixinIndex
{
    /**
     * @var string fieldsCacheKey
     */
    protected $fieldsCacheKey = 'fields';

    /**
     * listMixins
     */
    public function listMixins(): array
    {
        $result = [];

        foreach ($this->listMixinsRaw() as $attributes) {
            $result[] = MixinBlueprint::newFromIndexer($attributes);
        }

        return $result;
    }

    /**
     * listMixinsRaw without populating the Blueprint object
     */
    protected function listMixinsRaw(): array
    {
        $records = $this->getCache($this->fieldsCacheKey);

        if (!$records) {
            $records = $this->indexMixins();
        }

        return $records;
    }

    /**
     * hasMixin is a quick way to resolve a mixin to a UUID
     */
    public function hasMixin($handleOrUuid): string
    {
        $index = $this->listMixinsRaw();

        // UUID found
        if (isset($index[$handleOrUuid])) {
            return $handleOrUuid;
        }

        // Handle found
        foreach ($index as $attributes) {
            if (isset($attributes['handle']) && $attributes['handle'] === $handleOrUuid) {
                return $attributes['uuid'] ?? '';
            }
        }

        return '';
    }

    /**
     * findMixin
     */
    public function findMixin($uuid): ?MixinBlueprint
    {
        $index = $this->listMixinsRaw();

        if (!isset($index[$uuid])) {
            return null;
        }

        return MixinBlueprint::newFromIndexer($index[$uuid]);
    }

    /**
     * indexMixins
     */
    public function indexMixins(): array
    {
        $newIndex = [];

        foreach (MixinBlueprint::listInProject() as $field) {
            $newIndex[$field->uuid] = $field->toArray();
        }

        $this->putCache($this->fieldsCacheKey, $newIndex);

        return $newIndex;
    }
}
