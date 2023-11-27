<?php namespace Tailor\Classes\Relations;

use Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use October\Rain\Database\Relations\MorphToMany;

/**
 * CustomMultiJoinRelation adds a field name, site identifier and relation morph.
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class CustomMultiJoinRelation extends MorphToMany
{
    /**
     * @var string fieldName
     */
    protected $fieldName;

    /**
     * __construct is inherited
     */
    public function __construct(
        Builder $query,
        Model $parent,
        $name,
        $table,
        $foreignKey,
        $otherKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
        $inverse = false
    ) {
        $foreignKey = $inverse ? 'parent_id' : 'relation_id';
        $otherKey = $inverse ? 'relation_id' : 'parent_id';

        $this->fieldName = $name;

        parent::__construct(
            $query,
            $parent,
            'relation',
            $table,
            $foreignKey,
            $otherKey,
            $parentKey,
            $relatedKey,
            $relationName,
            $inverse
        );
    }

    /**
     * addDefinedConstraintsToRelation
     */
    public function addDefinedConstraintsToRelation($relation, array $args = null)
    {
        parent::addDefinedConstraintsToRelation($relation, $args);

        $relation->withPivot(['site_id']);
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
    protected function addWhereConstraints(): CustomMultiJoinRelation
    {
        parent::addWhereConstraints();

        $this->wherePivot($this->table.'.field_name', $this->fieldName);

        return $this;
    }

    /**
     * addEagerConstraints
     */
    public function addEagerConstraints(array $models)
    {
        parent::addEagerConstraints($models);

        $this->wherePivot($this->table.'.field_name', $this->fieldName);
    }

    /**
     * baseAttachRecord creates a new pivot attachment record.
     * @param  int   $id
     * @param  bool  $timed
     * @return array
     */
    protected function baseAttachRecord($id, $timed)
    {
        return Arr::add(
            parent::baseAttachRecord($id, $timed),
            'field_name',
            $this->fieldName
        );
    }

    /**
     * getRelationExistenceQuery
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns)->where(
            $this->table.'.field_name',
            $this->fieldName
        );
    }

    /**
     * newPivotQuery creates a new query builder for the pivot table.
     */
    public function newPivotQuery()
    {
        return parent::newPivotQuery()->where($this->table.'.field_name', $this->fieldName);
    }

    /**
     * attach a model to the parent
     */
    public function attach($id, array $attributes = [], $touch = true)
    {
        $attributes += [
            'relation_type' => $this->morphClass,
            'field_name' => $this->fieldName
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
}
