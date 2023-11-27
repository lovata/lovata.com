<?php namespace Tailor\Classes\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use October\Rain\Database\Relations\HasOne;

/**
 * CustomFieldHasOneRelation adds a field name to has many
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class CustomFieldHasOneRelation extends HasOne
{
    /**
     * addConstraints on the relation query.
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->getRelationQuery()->where('host_field', $this->relationName);

            parent::addConstraints();
        }
    }

    /**
     * addEagerConstraints for an eager load of the relation.
     */
    public function addEagerConstraints(array $models)
    {
        parent::addEagerConstraints($models);

        $this->getRelationQuery()->where('host_field', $this->relationName);
    }

    /**
     * setForeignAttributesForCreate a related model.
     */
    protected function setForeignAttributesForCreate(Model $model)
    {
        $model->{$this->getForeignKeyName()} = $this->getParentKey();

        $model->host_field = $this->relationName;
    }

    /**
     * getRelationExistenceQuery
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns)->where(
            $query->qualifyColumn('host_field'),
            $this->relationName
        );
    }
}
