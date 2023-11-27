<?php namespace Tailor\Classes\SchemaBuilder;

use Schema;

/**
 * HasRepeaterTable
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasRepeaterTable
{
    /**
     * migrateRepeaters
     */
    public function migrateRepeaters()
    {
        $tableName = $this->blueprint->getRepeaterTableName();
        $tableExists = Schema::hasTable($tableName);

        if ($tableExists) {
            return;
        }

        Schema::create($tableName, function ($table) {
            $this->defineRepeaterTableColumns($table);
            $this->defineTableComment($table, "Repeaters for :name [:id].");
        });

        $this->actionCount++;
    }

    /**
     * defineRepeaterTableColumns
     */
    protected function defineRepeaterTableColumns($table)
    {
        $table->increments('id');
        $table->integer('host_id')->nullable();
        $table->string('host_field')->nullable();
        $table->integer('site_id')->nullable()->index();
        $table->string('content_group')->nullable();
        $table->mediumText('content_value')->nullable();
        $table->text('content_spawn_path')->nullable();
        $table->integer('sort_order')->nullable();
        $table->timestamps();
        $table->index(['host_id', 'host_field'], $table->getTable().'_idx');
    }
}
