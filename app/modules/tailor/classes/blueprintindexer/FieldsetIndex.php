<?php namespace Tailor\Classes\BlueprintIndexer;

use Tailor\Classes\Blueprint\GlobalBlueprint;
use Tailor\Classes\Blueprint\EntryBlueprint;
use Tailor\Classes\FieldManager;
use Tailor\Classes\Fieldset;

/**
 * FieldsetIndex
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait FieldsetIndex
{
    /**
     * @var string fieldsetsCacheKey
     */
    protected $fieldsetsCacheKey = 'fieldsets';

    /**
     * listFieldsets
     */
    public function listFieldsets(): array
    {
        $manager = FieldManager::instance();

        $result = [];
        foreach ($this->listFieldsetsRaw() as $attributes) {
            $result[] = $manager->makeFieldset($attributes);
        }

        return $result;
    }

    /**
     * listFieldsetsRaw without populating the Blueprint object
     */
    protected function listFieldsetsRaw(): array
    {
        $records = $this->getCache($this->fieldsetsCacheKey);

        if (!$records) {
            $records = $this->indexFieldsets();
        }

        return $records;
    }

    /**
     * findFieldset
     */
    public function findFieldset(string $uuid, string $group = null): ?Fieldset
    {
        $index = $this->listFieldsetsRaw();

        $uuidKey = $group !== null
            ? $uuid . ':' . $group
            : $uuid;

        if (!isset($index[$uuidKey])) {
            return null;
        }

        return FieldManager::instance()->makeFieldset($index[$uuidKey]);
    }

    /**
     * findContentFieldset creates a compilation of fields based on shared content groups
     * that is compiled on a first seen basis.
     */
    public function findContentFieldset($contentUuid): ?Fieldset
    {
        $fieldset = null;
        $config = null;

        foreach ($this->listFieldsetsRaw() as $uuid => $attributes) {
            if (!isset($attributes['contentUuid']) || $attributes['contentUuid'] !== $contentUuid) {
                continue;
            }

            if ($config === null) {
                $config = $attributes;
            }
            else {
                $config['fields'] += $attributes['fields'] ?? [];
            }
        }

        if ($config !== null) {
            $fieldset = FieldManager::instance()->makeFieldset($config);
        }

        return $fieldset;
    }

    /**
     * indexFieldsets
     */
    public function indexFieldsets(): array
    {
        $newIndex = $this->findAllFieldsetBlueprints();

        $this->putCache($this->fieldsetsCacheKey, $newIndex);

        return $newIndex;
    }

    /**
     * findAllFieldsetBlueprints will spin over all fieldset sources
     */
    protected function findAllFieldsetBlueprints(): array
    {
        $result = [];

        // Sections
        foreach (EntryBlueprint::listInProject() as $section) {
            if (is_array($section->groups)) {
                foreach ($section->groups as $group => $definition) {
                    $uuid = $section->uuid . ':' . $group;
                    $result[$uuid] = [
                        'name' => $definition['name'] ?? $section->name,
                        'handle' => $group,
                        'contentUuid' => $section->uuid,
                        'columns' => $section->columns,
                        'scopes' => $section->scopes,
                        'validation' => $section->validation
                    ] + $definition;
                }
            }
            else {
                $result[$section->uuid] = [
                    'name' => $section->name,
                    'handle' => $section->handle,
                    'contentUuid' => $section->uuid,
                    'fields' => $section->fields,
                    'columns' => $section->columns,
                    'scopes' => $section->scopes,
                    'validation' => $section->validation
                ];
            }
        }

        // Globals
        foreach (GlobalBlueprint::listInProject() as $global) {
            $result[$global->uuid] = [
                'name' => $global->name,
                'handle' => $global->handle,
                'contentUuid' => $global->uuid,
                'fields' => $global->fields,
                'validation' => $global->validation
            ];
        }

        return $result;
    }
}
