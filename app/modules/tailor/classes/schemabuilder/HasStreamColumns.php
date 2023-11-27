<?php namespace Tailor\Classes\SchemaBuilder;

/**
 * HasStreamColumns
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasStreamColumns
{
    /**
     * defineStreamColumns
     */
    protected function defineStreamColumns($table)
    {
        if (!$this->hasColumn('published_at_day')) {
            $table->integer('published_at_day')->nullable();
        }

        if (!$this->hasColumn('published_at_month')) {
            $table->integer('published_at_month')->nullable();
        }

        if (!$this->hasColumn('published_at_year')) {
            $table->integer('published_at_year')->nullable();
        }
    }
}
