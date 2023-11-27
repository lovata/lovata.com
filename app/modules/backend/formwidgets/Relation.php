<?php namespace Backend\FormWidgets;

use Db;
use DbDongle;
use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use SystemException;

/**
 * Relation renders a field pre-populated with a belongsTo and belongsToHasMany relation
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Relation extends FormWidgetBase
{
    use \Backend\Traits\FormModelWidget;

    //
    // Configurable Properties
    //

    /**
     * @var bool useController to completely replace this widget the `RelationController` behavior.
     */
    public $useController;

    /**
     * @var string nameFrom is the model column to use for the name reference
     */
    public $nameFrom = 'name';

    /**
     * @var string sqlSelect is the custom SQL column selection to use for the name reference
     */
    public $sqlSelect;

    /**
     * @var string emptyOption to use if the relation is singular (belongsTo)
     */
    public $emptyOption;

    /**
     * @var string scope method for the list query.
     */
    public $scope;

    /**
     * @var string conditions filters the relation using a raw where query statement.
     */
    public $conditions;

    /**
     * @var mixed defaultSort column to look for.
     */
    public $defaultSort;

    //
    // Object Properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'relation';

    /**
     * @var FormField renderFormField object used for rendering a simple field type
     */
    public $renderFormField;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'nameFrom',
            'emptyOption',
            'defaultSort',
            'scope',
            'conditions',
        ]);

        if (isset($this->config->select)) {
            $this->sqlSelect = $this->config->select;
        }

        $this->useController = $this->evalUseController($this->config->useController ?? true);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('relation');
    }

    /**
     * prepareVars for display
     */
    public function prepareVars()
    {
        if ($this->useController) {
            return;
        }

        $this->vars['field'] = $this->makeRenderFormField();
    }

    /**
     * evalUseController determines if the relation controller is usable and returns the default
     * preference if it can be used.
     */
    protected function evalUseController(bool $defaultPref): bool
    {
        if (!$this->controller->isClassExtendedWith(\Backend\Behaviors\RelationController::class)) {
            return false;
        }

        if (!is_string($this->valueFrom)) {
            return false;
        }

        if (!$this->controller->relationHasField($this->valueFrom)) {
            return false;
        }

        return $defaultPref;
    }

    /**
     * makeRenderFormField for rendering a simple field type
     */
    protected function makeRenderFormField()
    {
        $field = clone $this->formField;
        [$model, $attribute] = $this->resolveModelAttribute($this->valueFrom);

        $relationObject = $this->getRelationObject();
        $relationType = $model->getRelationType($attribute);
        $relationModel = $model->makeRelation($attribute);
        $query = $relationModel->newQuery();

        if (in_array($relationType, ['belongsToMany', 'morphedByMany', 'morphToMany', 'hasMany'])) {
            $field->type = 'checkboxlist';
        }
        elseif (in_array($relationType, ['belongsTo', 'hasOne', 'morphOne'])) {
            $field->type = 'dropdown';
        }
        else {
            throw new SystemException("Could not translate relation type '{$relationType}' to a valid field type");
        }

        // Sort the query using configuration
        if ($this->defaultSort) {
            $this->applyDefaultSortToQuery($query);
        }

        // It is safe to assume that if the model and related model are of
        // the exact same class, then it cannot be related to itself
        if ($model->exists && ($relationModel->getTable() === $model->getTable())) {
            $query->where($relationModel->getKeyName(), '<>', $model->getKey());
        }

        if ($sqlConditions = $this->conditions) {
            $query->whereRaw(DbDongle::parse($sqlConditions, $model->attributes));
        }
        elseif ($scopeMethod = $this->scope) {
            if (
                is_string($scopeMethod) &&
                count($staticMethod = explode('::', $scopeMethod)) === 2 &&
                is_callable($staticMethod)
            ) {
                $staticMethod($query, $model);
            }
            elseif (is_string($scopeMethod)) {
                $query->$scopeMethod($model);
            }
            else {
                $scopeMethod($query, $model);
            }
        }
        else {
            $relationObject->addDefinedConstraintsToQuery($query);

            // Reset any orders that may have come from these definitions
            // because it has a tendency to break things.
            if (in_array($relationType, ['belongsToMany', 'morphedByMany', 'morphToMany'])) {
                $query->getQuery()->reorder();
            }
        }

        // Determine if the model uses a tree trait
        $usesTree = $relationModel->isClassInstanceOf(\October\Contracts\Database\TreeInterface::class);

        // The "sqlSelect" config takes precedence over "nameFrom".
        // A virtual column called "selection" will contain the result.
        // Tree models must select all columns to return parent columns, etc.
        if ($this->sqlSelect) {
            $nameFrom = 'selection';
            $selectColumn = $usesTree ? '*' : $relationModel->getKeyName();
            $selectSql = $this->sqlSelect;
            $result = $query->select($selectColumn, DbDongle::raw($selectSql . ' as ' . $nameFrom));
        }
        else {
            $nameFrom = $this->nameFrom;
            $result = $query->get();
        }

        // Relations can specify a custom local or foreign "other" key,
        // which can be detected and implemented here automatically.
        if (in_array($relationType, ['belongsTo'])) {
            $primaryKeyName = $relationObject->getOwnerKeyName();
        }
        elseif (in_array($relationType, ['hasMany', 'hasOne', 'belongsToMany', 'morphedByMany', 'morphToMany'])) {
            $primaryKeyName = $relationObject->getRelatedKeyName();
        }
        else {
            $primaryKeyName = $relationModel->getKeyName();
        }

        $field->options = $usesTree
            ? $result->listsNested($nameFrom, $primaryKeyName)
            : $result->pluck($nameFrom, $primaryKeyName)->all();

        return $this->renderFormField = $field;
    }

    /**
     * applyDefaultSortToQuery
     */
    protected function applyDefaultSortToQuery($query)
    {
        if (is_string($this->defaultSort)) {
            $query->orderBy($this->defaultSort, 'desc');
        }
        elseif (is_array($this->defaultSort) && isset($this->defaultSort['column'])) {
            $query->orderBy($this->defaultSort['column'], $this->defaultSort['direction'] ?? 'desc');
        }
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        if (is_string($value) && !strlen($value)) {
            return null;
        }

        if (is_array($value) && !count($value)) {
            return null;
        }

        return $value;
    }
}
