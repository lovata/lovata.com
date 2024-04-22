<?php namespace Tailor\Classes\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use October\Rain\Database\Relations\BelongsToMany;

/**
 * CustomNestedJoinRelation is used by tailor records, creating relationships
 * to nested content joins via the "tailor_content_joins" table.
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class CustomNestedJoinRelation extends BelongsToMany
{
    /**
     * @var string parentClass is the class name of the parent morph type (inverse)
     */
    protected $parentClass;

    /**
     * @var string morphClass is the class name of the morph type
     */
    protected $morphClass;

    /**
     * __construct is inherited
     */
    public function __construct(
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null
    ) {
        $foreignPivotKey = 'parent_id';
        $relatedPivotKey = 'relation_id';

        $this->parentClass = $parent->getMorphClass();
        $this->morphClass = $query->getModel()->getMorphClass();

        parent::__construct(
            $query,
            $parent,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relationName
        );
    }

    /**
     * addDefinedConstraintsToRelation
     */
    public function addDefinedConstraintsToRelation($relation, array $args = null)
    {
        parent::addDefinedConstraintsToRelation($relation, $args);

        $relation->withPivot(['field_name', 'parent_type', 'relation_type', 'site_id']);
    }

    /**
     * addDefinedConstraintsToQuery
     */
    public function addDefinedConstraintsToQuery($query, array $args = null)
    {
        parent::addDefinedConstraintsToQuery($query, $args);
    }

    /**
     * newSimpleRelationQuery
     */
    protected function newSimpleRelationQuery(array $ids)
    {
        $query = parent::newSimpleRelationQuery($ids);

        parent::addDefinedConstraintsToQuery($query);

        return $query;
    }

    /**
     * addWhereConstraints sets the where clause for the relation query
     */
    protected function addWhereConstraints(): CustomNestedJoinRelation
    {
        parent::addWhereConstraints();

        $this->where($this->qualifyPivotColumn('parent_type'), $this->parentClass);
        $this->where($this->qualifyPivotColumn('relation_type'), $this->morphClass);
        $this->where($this->qualifyPivotColumn('field_name'), $this->relationName);

        return $this;
    }

    /**
     * getRelationExistenceQuery
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns)
            ->where($this->qualifyPivotColumn('parent_type'), $this->parentClass)
            ->where($this->qualifyPivotColumn('relation_type'), $this->morphClass)
            ->where($this->qualifyPivotColumn('field_name'), $this->relationName)
        ;
    }

    /**
     * attach a model to the parent
     */
    public function attach($id, array $attributes = [], $touch = true)
    {
        $attributes += [
            'parent_type' => $this->parentClass,
            'relation_type' => $this->morphClass,
            'field_name' => $this->relationName
        ];

        return parent::attach($id, $attributes, $touch);
    }

    /**
     * addEagerConstraints
     */
    public function addEagerConstraints(array $models)
    {
        parent::addEagerConstraints($models);

        $this->where($this->qualifyPivotColumn('parent_type'), $this->parentClass);
        $this->where($this->qualifyPivotColumn('relation_type'), $this->morphClass);
        $this->where($this->qualifyPivotColumn('field_name'), $this->relationName);
    }
}
