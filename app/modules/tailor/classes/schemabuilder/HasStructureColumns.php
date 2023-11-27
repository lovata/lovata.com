<?php namespace Tailor\Classes\SchemaBuilder;

/**
 * HasStructureColumns
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasStructureColumns
{
    /**
     * defineStructureColumns
     */
    protected function defineStructureColumns($table)
    {
        if (!$this->hasColumn('fullslug')) {
            $table->string('fullslug')->nullable()->index();
        }

        if (!$this->hasColumn('parent_id')) {
            $table->integer('parent_id')->nullable();
        }

        if (!$this->hasColumn('nest_left')) {
            $table->integer('nest_left')->nullable();
        }

        if (!$this->hasColumn('nest_right')) {
            $table->integer('nest_right')->nullable();
        }

        if (!$this->hasColumn('nest_depth')) {
            $table->integer('nest_depth')->nullable();
        }
    }
}
