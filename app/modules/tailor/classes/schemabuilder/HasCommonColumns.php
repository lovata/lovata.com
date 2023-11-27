<?php namespace Tailor\Classes\SchemaBuilder;

/**
 * HasCommonColumns
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasCommonColumns
{
    /**
     * defineStartColumns
     */
    protected function defineStartColumns($table)
    {
        if (!$this->hasColumn('id')) {
            $table->increments('id');
        }

        if (!$this->hasColumn('site_id')) {
            $table->integer('site_id')->nullable()->index();
        }

        if (!$this->hasColumn('site_root_id')) {
            $table->integer('site_root_id')->nullable()->index();
        }

        if (!$this->hasColumn('blueprint_uuid')) {
            $table->string('blueprint_uuid')->nullable()->index();
        }

        if (!$this->hasColumn('content_group')) {
            $table->string('content_group')->nullable()->index();
        }

        if (!$this->hasColumn('title')) {
            $table->string('title')->nullable();
        }

        if (!$this->hasColumn('slug')) {
            $table->string('slug')->nullable()->index();
        }
    }

    /**
     * defineEndColumns
     */
    protected function defineEndColumns($table)
    {
        if (!$this->hasColumn('created_user_id')) {
            $table->integer('created_user_id')->unsigned()->nullable();
        }

        if (!$this->hasColumn('updated_user_id')) {
            $table->integer('updated_user_id')->unsigned()->nullable();
        }

        if (!$this->hasColumn('deleted_user_id')) {
            $table->integer('deleted_user_id')->unsigned()->nullable();
        }

        if (!$this->hasColumn('deleted_at')) {
            $table->softDeletes();
        }

        if (!$this->hasColumn('created_at')) {
            $table->timestamps();
        }
    }

    /**
     * defineVisibilityColumns
     */
    protected function defineVisibilityColumns($table)
    {
        if (!$this->hasColumn('is_enabled')) {
            $table->boolean('is_enabled')->nullable();
        }

        if (!$this->hasColumn('published_at')) {
            $table->timestamp('published_at')->nullable();
        }

        if (!$this->hasColumn('published_at_date')) {
            $table->timestamp('published_at_date')->nullable();
        }

        if (!$this->hasColumn('expired_at')) {
            $table->timestamp('expired_at')->nullable();
        }
    }

    /**
     * defineDraftableColumns
     */
    protected function defineDraftableColumns($table)
    {
        if (!$this->hasColumn('draft_mode')) {
            $table->integer('draft_mode')->default(1);
        }

        if (!$this->hasColumn('primary_id')) {
            $table->integer('primary_id')->unsigned()->nullable()->index();
            $this->tableColumns[] = 'primary_id';
        }

        if (!$this->hasColumn('primary_attrs')) {
            $table->mediumText('primary_attrs')->nullable();
            $this->tableColumns[] = 'primary_attrs';
        }
    }

    /**
     * defineVersionableColumns
     */
    protected function defineVersionableColumns($table)
    {
        if (!$this->hasColumn('is_version')) {
            $table->boolean('is_version')->default(false);
        }

        if (!$this->hasColumn('primary_id')) {
            $table->integer('primary_id')->unsigned()->nullable()->index();
            $this->tableColumns[] = 'primary_id';
        }

        if (!$this->hasColumn('primary_attrs')) {
            $table->mediumText('primary_attrs')->nullable();
            $this->tableColumns[] = 'primary_attrs';
        }
    }
}
