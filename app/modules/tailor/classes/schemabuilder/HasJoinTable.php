<?php namespace Tailor\Classes\SchemaBuilder;

use Schema;

/**
 * HasJoinTable
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasJoinTable
{
    /**
     * migrateJoins
     */
    public function migrateJoins()
    {
        $tableName = $this->blueprint->getJoinTableName();
        $tableExists = Schema::hasTable($tableName);

        if ($tableExists) {
            return;
        }

        Schema::create($tableName, function ($table) {
            $this->defineJoinTableColumns($table);
            $this->defineTableComment($table, "Joins for :name [:id].");
        });

        $this->actionCount++;
    }

    /**
     * defineJoinTableColumns
     */
    protected function defineJoinTableColumns($table)
    {
        $table->integer('parent_id')->nullable();
        $table->integer('relation_id')->nullable();
        $table->string('relation_type')->nullable();
        $table->string('field_name')->nullable()->index();
        $table->integer('site_id')->nullable()->index();

        $table->index(['relation_id', 'relation_type'], $table->getTable().'_idx');
    }
}
