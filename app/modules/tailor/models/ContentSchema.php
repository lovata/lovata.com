<?php namespace Tailor\Models;

use Model;
use October\Rain\Database\Schema\Blueprint as DbBlueprint;

/**
 * ContentSchema record
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class ContentSchema extends Model
{
    /**
     * @var string table associated with the model
     */
    protected $table = 'tailor_content_schema';

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = ['meta', 'fields'];

    /**
     * @var array latestMeta data
     */
    protected $latestMeta = [];

    /**
     * @var array proposedFields
     */
    protected $proposedFields = [];

    /**
     * @var array droppedFields
     */
    protected $droppedFields = [];

    /**
     * @var array existingColumns
     */
    protected $existingColumns = [];

    /**
     * @var array toCreate
     */
    protected $toCreate = [];

    /**
     * @var array droppedFields
     */
    protected $toDrop = [];

    /**
     * @var array changedFields
     */
    protected $toChange = [];

    /**
     * findRecord
     */
    public static function findRecord(string $tableName): ContentSchema
    {
        $record = self::where('table_name', $tableName)->first();

        if (!$record) {
            $record = new self;
            $record->table_name = $tableName;
            $record->save();
        }

        return $record;
    }

    /**
     * afterFetch
     */
    public function afterFetch()
    {
        $this->latestMeta = $this->meta ?? [];
        $this->proposedFields = $this->fields['active'] ?? [];
        $this->droppedFields = $this->fields['dropped'] ?? [];
    }

    /**
     * setExistingColumns
     */
    public function setExistingColumns(array $columns)
    {
        $this->existingColumns = $columns;
    }

    /**
     * setDroppedColumn
     */
    public function setDroppedColumn($fieldName, $droppedName, $isRename = false)
    {
        if ($isRename) {
            $this->droppedFields[$droppedName] = [
                'original' => $fieldName,
                'renamed' => true,
                'details' => $this->proposedFields[$fieldName] ?? null
            ];
        }
        else {
            $this->droppedFields[$droppedName] = [
                'original' => $fieldName,
                'details' => $this->proposedFields[$fieldName] ?? null
            ];

            unset($this->proposedFields[$fieldName]);
        }
    }

    /**
     * proposeChanges returns true if changes are needed
     */
    public function proposeChanges(DbBlueprint $table): bool
    {
        // Extract fields from blueprint
        $fields = [];
        foreach ($table->getColumns() as $column) {
            if (isset($column['name'])) {
                $fields[$column['name']] = $column->toArray();
            }
        }

        // Compare for existing schema
        foreach ($this->proposedFields as $fieldName => $details) {
            if (isset($fields[$fieldName])) {
                continue;
            }

            $this->toDrop[$fieldName] = $details;
        }

        // Propose for new schema
        foreach ($fields as $fieldName => $details) {
            if (!in_array($fieldName, $this->existingColumns)) {
                $this->toCreate[$fieldName] = $this->proposedFields[$fieldName] = $details;
                continue;
            }

            $wantType = $details['type'] ?? null;
            if (!$wantType) {
                $this->toDrop[$fieldName] = $details;
                continue;
            }

            $hasType = $this->proposedFields[$fieldName]['type'] ?? null;
            if (!$hasType || $wantType !== $hasType) {
                $this->toChange[$fieldName] = $this->proposedFields[$fieldName] = $details;
                continue;
            }
        }

        return $this->toCreate || $this->toDrop || $this->toChange;
    }

    /**
     * commitChanges
     */
    public function commitChanges()
    {
        $this->fields = [
            'active' => $this->proposedFields,
            'dropped' => $this->droppedFields
        ];

        $this->meta = $this->latestMeta;

        $this->save();
    }

    /**
     * getMissingFields returns missing fields as an array
     * eg: [fieldName => fieldDetails]
     */
    public function getMissingFields(): array
    {
        return $this->toCreate;
    }

    /**
     * getDroppedFields returns fields that should be dropped
     * eg: [fieldName => fieldDetails]
     */
    public function getDroppedFields(): array
    {
        return $this->toDrop;
    }

    /**
     * getChangedFields returns fields that have a new type
     */
    public function getChangedFields(): array
    {
        return $this->toChange;
    }

    /**
     * setLatestMeta will set the data upon commit
     */
    public function setLatestMeta(array $meta)
    {
        $this->latestMeta = $meta;
    }

    /**
     * getPruneFields returns fields that should be pruned after migration
     */
    public function getPruneFields(): array
    {
        return $this->droppedFields;
    }

    /**
     * setPrunedColumn removes a column after pruning it
     */
    public function setPrunedColumn(string $fieldName)
    {
        unset($this->droppedFields[$fieldName]);
    }

    /**
     * isMetaDirty returns true if there are proposed meta changes
     */
    public function isMetaDirty($value = null)
    {
        if ($value === null) {
            return $this->latestMeta !== $this->meta;
        }

        $newMeta = array_get($this->latestMeta, $value);
        $oldMeta = array_get($this->meta, $value);
        return $newMeta !== $oldMeta;
    }

    /**
     * getMetaData returns the original meta data value
     */
    public function getMetaData($value = null)
    {
        if ($value === null) {
            return $this->meta;
        }

        return array_get($this->meta, $value);
    }
}
