<?php namespace Tailor\ContentFields;

use Lang;
use Tailor\Models\RepeaterItem;
use Tailor\Classes\Relations\CustomMultiJoinRelation;
use Tailor\Classes\Relations\CustomNestedJoinRelation;
use October\Contracts\Element\FormElement;
use October\Contracts\Element\ListElement;
use October\Contracts\Element\FilterElement;
use SystemException;

/**
 * RecordFinderField allows association to models
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class RecordFinderField extends FallbackField
{
    /**
     * @var string modelClass of the model to use for listing records
     */
    public $modelClass;

    /**
     * @var int|null maxItems allowed
     */
    public $maxItems;

    /**
     * @var string inverse the relationship definition, set to the field name
     */
    public $inverse;

    /**
     * @var string displayMode for the relationship
     */
    public $displayMode = 'recordfinder';

    /**
     * @var array controller config for displayMode: controller
     */
    public $controller = [];

    /**
     * defineConfig will process the field configuration.
     */
    public function defineConfig(array $config)
    {
        if (isset($config['modelClass'])) {
            $this->modelClass = (string) $config['modelClass'];
        }

        if (isset($config['maxItems'])) {
            $this->maxItems = (int) $config['maxItems'];
        }

        if (isset($config['inverse'])) {
            $this->inverse = (string) $config['inverse'];
        }

        if (isset($config['displayMode'])) {
            $this->displayMode = (string) $config['displayMode'];
        }

        if (isset($config['controller'])) {
            $this->controller = (array) $config['controller'];
        }
    }

    /**
     * defineFormField will define how a field is displayed in a form.
     */
    public function defineFormField(FormElement $form, $context = null)
    {
        $field = $form->addFormField($this->fieldName, $this->label);

        $config = $this->config + ['nameFrom' => 'title'];

        $field->useConfig($config);

        // Singular and multi display modes
        $supportedDisplays = $this->maxItems === 1
            ? ['recordfinder']
            : ['taglist'];

        $field->displayAs(in_array($this->displayMode, $supportedDisplays)
            ? $this->displayMode
            : 'relation');

        if ($this->displayMode === 'controller') {
            $this->defineFormFieldAsRelationController($field);
        }

        // @deprecated this should be default
        if ($field->type === 'taglist') {
            $field->customTags(false);
        }
    }

    /**
     * defineListColumn
     */
    public function defineListColumn(ListElement $list, $context = null)
    {
        $list->defineColumn($this->fieldName, $this->label)
            ->relation($this->fieldName)
            ->sqlSelect($this->nameFrom ?: 'title')
            ->sortable(false)
            ->shortLabel($this->shortLabel)
            ->useConfig($this->column ?: [])
        ;
    }

    /**
     * defineFilterScope
     */
    public function defineFilterScope(FilterElement $filter, $context = null)
    {
        $filter->defineScope($this->fieldName, $this->label)
            ->displayAs('group')
            ->nameFrom('title')
            ->shortLabel($this->shortLabel)
            ->useConfig($this->scope ?: [])
        ;
    }

    /**
     * extendModelObject will extend the record model.
     */
    public function extendModelObject($model)
    {
        // Define the relationship
        if ($this->inverse) {
            $this->defineInverseModelRelationship($model);
        }
        else {
            $this->defineModelRelationship($model);
        }
    }

    /**
     * extendDatabaseTable
     */
    public function extendDatabaseTable($table)
    {
        if ($this->maxItems === 1) {
            $table->integer($this->getSingularKeyName())->unsigned()->nullable();
        }
    }

    /**
     * getSingularKeyName
     */
    public function getSingularKeyName()
    {
        return $this->fieldName.'_id';
    }

    /**
     * defineModelRelationship for the direct relationship. These definitions
     * rely on multisite modifying the relation definition.
     *
     * @see \October\Rain\Database\Traits\Multisite::defineMultisiteRelation
     */
    protected function defineModelRelationship($model)
    {
        $relatedModel = $this->getRelatedModel();
        $relatedMultisite = $relatedModel->isClassInstanceOf(\October\Contracts\Database\MultisiteInterface::class) &&
            $relatedModel->isMultisiteEnabled();

        $isSingular = $this->maxItems === 1;
        $isNested = $model instanceof RepeaterItem;

        if ($isSingular) {
            $model->belongsTo[$this->fieldName] = [
                $relatedModel::class,
                'key' => $this->getSingularKeyName()
            ];
        }
        elseif ($isNested) {
            $model->belongsToMany[$this->fieldName] = [
                $relatedModel::class,
                'table' => 'tailor_content_joins',
                'relationClass' => CustomNestedJoinRelation::class,
                'relatedKey' => $relatedMultisite ? 'site_root_id' : 'id'
            ];
        }
        else {
            $model->morphedByMany[$this->fieldName] = [
                $relatedModel::class,
                'table' => $model->getBlueprintDefinition()->getJoinTableName(),
                'name' => $this->fieldName,
                'relationClass' => CustomMultiJoinRelation::class,
                'relatedKey' => $relatedMultisite ? 'site_root_id' : 'id'
            ];
        }
    }

    /**
     * defineInverseModelRelationship for the inverse relationship. These definitions
     * do not rely on multisite via propagatable attribute. Inverse relations do not
     * replicate and should be treated as read-only.
     */
    protected function defineInverseModelRelationship($model)
    {
        $relatedModel = $this->getRelatedModel();
        if (!$relatedModel->hasRelation($this->inverse)) {
            throw new SystemException("Missing relation definition for inverse field '{$this->inverse}' for model class '{$this->modelClass}' for '{$this->fieldName}'.");
        }

        $parentMultisite = $model->getBlueprintDefinition()->useMultisite();
        $relationDefinition = $relatedModel->getRelationDefinition($this->inverse);
        $relatedMultisite = $relatedModel->isClassInstanceOf(\October\Contracts\Database\MultisiteInterface::class) &&
            $relatedModel->isMultisiteEnabled();

        $isSingular = $this->maxItems === 1;
        $otherIsSingular = $relatedModel->isRelationTypeSingular($this->inverse);
        $otherIsPropagatable = $relatedMultisite ? $relatedModel->isAttributePropagatable($this->inverse) : false;

        if ($isSingular) {
            $model->hasOne[$this->fieldName] = [
                $relatedModel::class,
                'key' => $relationDefinition['key'] ?? null,
                'otherKey' => $otherIsPropagatable ? 'site_root_id' : 'id',
                'replicate' => false
            ];
        }
        elseif ($otherIsSingular) {
            $model->hasMany[$this->fieldName] = [
                $relatedModel::class,
                'key' => $relationDefinition['key'] ?? null,
                'otherKey' => $otherIsPropagatable ? 'site_root_id' : 'id',
                'replicate' => false
            ];
        }
        else {
            $model->morphToMany[$this->fieldName] = [
                $relatedModel::class,
                'table' => $relationDefinition['table'] ?? null,
                'name' => $this->inverse,
                'relationClass' => CustomMultiJoinRelation::class,
                'relatedKey' => $otherIsPropagatable ? 'site_root_id' : 'id',
                'parentKey' => $parentMultisite ? 'site_root_id' : 'id',
                'replicate' => false
            ];
        }
    }

    /**
     * defineFormFieldAsRelationController
     */
    protected function defineFormFieldAsRelationController($field)
    {
        $customMessages = (array) $this->customMessages;

        $toolbarButtons = $this->toolbarButtons;

        $fieldConfig = [
            'label' => $this->label,
            'list' => $this->list ?: [],
            'form' => $this->form ?: [],
            'customMessages' => $customMessages,
            'popupSize' => $this->popupSize,
            'view' => [
                'toolbarButtons' => $toolbarButtons,
                'recordsPerPage' => $this->recordsPerPage,
            ],
            'manage' => [
                'recordsPerPage' => $this->recordsPerPage,
            ]
        ];

        // $relatedModel = $this->getRelatedModel();
        // if ($blueprint instanceof StructureBlueprint) {
        //     $fieldConfig['structure'] = [
        //         'maxDepth' => $blueprint->getMaxDepth(),
        //         'showTree' => $blueprint->hasTree(),
        //     ] + ((array) $blueprint->structure);
        // }

        if ($this->span === 'adaptive') {
            $fieldConfig['externalToolbarAppState'] = 'toolbarExtensionPoint';
        }

        // Transfer custom configuration
        $toTransfer = ['label', 'list', 'form', 'view', 'manage'];
        foreach ($toTransfer as $transfer) {
            if (isset($this->controller[$transfer])) {
                $fieldConfig[$transfer] = is_array($this->controller[$transfer])
                    ? array_merge($fieldConfig[$transfer], (array) $this->controller[$transfer])
                    : $this->controller[$transfer];
            }
        }

        $field->controller($fieldConfig);
    }

    /**
     * getRelatedModel returns the related model to use
     */
    protected function getRelatedModel()
    {
        if (!class_exists($this->modelClass)) {
            throw new SystemException(Lang::get('backend::lang.recordfinder.invalid_model_class', ['modelClass' => $this->modelClass]));
        }

        return new $this->modelClass;
    }
}
