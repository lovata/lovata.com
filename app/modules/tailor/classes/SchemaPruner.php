<?php namespace Tailor\Classes;

use Schema;
use Tailor\Classes\Blueprint;
use Tailor\Models\ContentSchema;

/**
 * SchemaPruner prunes tables for tailor content
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class SchemaPruner
{
    /**
     * @var array prunedTables
     */
    protected $prunedTables = [];

    /**
     * @var array prunedFields
     */
    protected $prunedFields = [];

    /**
     * getPrunedTables
     */
    public function getPrunedTables(): array
    {
        return (array) $this->prunedTables;
    }

    /**
     * getPrunedFields
     */
    public function getPrunedFields(): array
    {
        return (array) $this->prunedFields;
    }

    /**
     * pruneTables removes tables that have no associated blueprint
     */
    public static function pruneTables(): static
    {
        $pruner = new self;

        $schemas = ContentSchema::all();
        foreach ($schemas as $schema) {
            $pruner->pruneTable($schema);
        }

        return $pruner;
    }

    /**
     * pruneAll unused fields in a blueprint
     */
    public static function pruneAll(): static
    {
        $pruner = new self;

        $schemas = ContentSchema::all();
        foreach ($schemas as $schema) {
            $pruner->pruneFields($schema);
        }

        return $pruner;
    }

    /**
     * pruneBlueprint
     */
    public static function pruneBlueprint(Blueprint $blueprint): static
    {
        $pruner = new self;

        $tableName = $blueprint->getContentTableName();
        if (!$tableName) {
            return null;
        }

        $schema = ContentSchema::where('table_name', $tableName)->first();
        if (!$schema) {
            return null;
        }

        $pruner->pruneFields($schema);

        return $pruner;
    }

    /**
     * pruneFields
     */
    public function pruneFields(ContentSchema $schema)
    {
        $tableName = $schema->table_name;

        // Table is gone, schema goes too
        if (!Schema::hasTable($tableName)) {
            $schema->delete();
            return;
        }

        // Prune individual fields
        $tableColumns = Schema::getColumnListing($tableName);
        foreach ($schema->getPruneFields() as $fieldName => $details) {
            Schema::table($schema->table_name, function($table) use ($tableColumns, $fieldName) {
                if (in_array($fieldName, $tableColumns)) {
                    $table->dropColumn($fieldName);
                }
            });

            $this->prunedFields[$tableName][] = $fieldName;
            $schema->setPrunedColumn($fieldName);
        }

        $schema->commitChanges();
    }

    /**
     * pruneTable
     */
    public function pruneTable(ContentSchema $schema)
    {
        // Old schema detected
        // @deprecated remove if year >= 2025
        if (!$schema->getMetaData('blueprint_uuid')) {
            return;
        }

        // Found blueprint do nothing
        if ($this->findBlueprintFromSchema($schema)) {
            return;
        }

        // Dropping known tables
        $this->prunedTables[] = $contentTable = $schema->table_name;
        $this->prunedTables[] = $joinTable = substr($contentTable, 0, -1) . 'j';
        $this->prunedTables[] = $repeaterTable = substr($contentTable, 0, -1) . 'r';

        Schema::dropIfExists($repeaterTable);
        Schema::dropIfExists($joinTable);
        Schema::dropIfExists($contentTable);

        $schema->delete();
    }

    /**
     * findBlueprintFromSchema
     */
    protected function findBlueprintFromSchema(ContentSchema $schema): ?Blueprint
    {
        if ($blueprintUuid = $schema->getMetaData('blueprint_uuid')) {
            return BlueprintIndexer::instance()->find($blueprintUuid);
        }

        return null;
    }
}
