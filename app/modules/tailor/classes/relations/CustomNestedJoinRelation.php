<?php namespace Tailor\Classes\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use October\Rain\Database\Relations\BelongsToMany;

/**
 * CustomNestedJoinRelation adds a field name, site identifier and relation morph.
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

        $this->wherePivot('parent_type', $this->parentClass);
        $this->wherePivot('relation_type', $this->morphClass);
        $this->wherePivot('field_name', $this->relationName);

        return $this;
    }

    /**
     * getRelationExistenceQuery
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns)
            ->where($this->table.'.parent_type', $this->parentClass)
            ->where($this->table.'.relation_type', $this->morphClass)
            ->where($this->table.'.field_name', $this->relationName)
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
     * shouldSelect is modified to select all fields
     */
    protected function shouldSelect(array $columns = ['*'])
    {
        return array_merge($columns, $this->aliasedPivotColumns());
    }

    /**
     * addEagerConstraints
     */
    public function addEagerConstraints(array $models)
    {
        parent::addEagerConstraints($models);

        $this->wherePivot('parent_type', $this->parentClass);
        $this->wherePivot('relation_type', $this->morphClass);
        $this->wherePivot('field_name', $this->relationName);
    }
}
