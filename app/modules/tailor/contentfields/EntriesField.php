<?php namespace Tailor\ContentFields;

use Tailor\Models\EntryRecord;
use Tailor\Models\RepeaterItem;
use Tailor\Classes\BlueprintIndexer;
use Tailor\Classes\Relations\CustomMultiJoinRelation;
use Tailor\Classes\Relations\CustomNestedJoinRelation;
use October\Contracts\Element\FormElement;
use October\Contracts\Element\ListElement;
use October\Contracts\Element\FilterElement;
use SystemException;

/**
 * EntriesField allows association to entries
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class EntriesField extends FallbackField
{
    /**
     * @var string source UUID of the section
     */
    public $source;

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
     * @var mixed sourceCache of the source blueprint
     */
    protected $sourceCache;

    /**
     * @var mixed fieldsetCache of the source content fieldset definition
     */
    protected $fieldsetCache;

    /**
     * defineConfig will process the field configuration.
     */
    public function defineConfig(array $config)
    {
        if (isset($config['source'])) {
            $this->source = (string) $config['source'];
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
        $partial = $this->maxItems === 1 ? 'column_single' : 'column_multi';

        $list->defineColumn($this->fieldName, $this->label)
            ->displayAs('partial')
            ->path("~/modules/tailor/contentfields/entriesfield/partials/_{$partial}.php")
            ->clickable(false)
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

        // For defining list columns and form fields
        $model->bindEvent('model.afterRelation', function($name, $related) {
            if ($name === $this->fieldName) {
                $related->extendWithBlueprint($this->getSourceBlueprint()->uuid);
            }
        });
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
     * defineModelRelationship
     */
    protected function defineModelRelationship($model)
    {
        $useMultisite = $this->getSourceBlueprint()->useMultisite();
        $isSingular = $this->maxItems === 1;
        $isNested = $model instanceof RepeaterItem;

        if ($isSingular) {
            $model->belongsTo[$this->fieldName] = [
                EntryRecord::class,
                'key' => $this->getSingularKeyName(),
                'otherKey' => $useMultisite ? 'site_root_id' : 'id'
            ];
        }
        elseif ($isNested) {
            $model->belongsToMany[$this->fieldName] = [
                EntryRecord::class,
                'table' => 'tailor_content_joins',
                'relationClass' => CustomNestedJoinRelation::class,
                'relatedKey' => $useMultisite ? 'site_root_id' : 'id'
            ];
        }
        else {
            $model->morphedByMany[$this->fieldName] = [
                EntryRecord::class,
                'table' => $model->getBlueprintDefinition()->getJoinTableName(),
                'name' => $this->fieldName,
                'relationClass' => CustomMultiJoinRelation::class,
                'relatedKey' => $useMultisite ? 'site_root_id' : 'id'
            ];
        }
    }

    /**
     * defineInverseModelRelationship
     */
    protected function defineInverseModelRelationship($model)
    {
        $otherField = $this->getSourceFieldset()->getField($this->inverse);
        if (!$otherField || !$otherField instanceof EntriesField) {
            throw new SystemException("Invalid inverse field '{$this->inverse}' for source '{$this->source}' for '{$this->fieldName}'.");
        }

        $useMultisite = $model->getBlueprintDefinition()->useMultisite();
        $isSingular = $this->maxItems === 1;
        $otherIsSingular = $otherField->maxItems === 1;

        if ($isSingular) {
            $model->hasOne[$this->fieldName] = [
                EntryRecord::class,
                'key' => $otherField->getSingularKeyName(),
                'otherKey' => $useMultisite ? 'site_root_id' : 'id'
            ];
        }
        elseif ($otherIsSingular) {
            $model->hasMany[$this->fieldName] = [
                EntryRecord::class,
                'key' => $otherField->getSingularKeyName(),
                'otherKey' => $useMultisite ? 'site_root_id' : 'id'
            ];
        }
        else {
            $model->morphToMany[$this->fieldName] = [
                EntryRecord::class,
                'table' => $this->getSourceBlueprint()->getJoinTableName(),
                'name' => $this->inverse,
                'relationClass' => CustomMultiJoinRelation::class,
                'parentKey' => $useMultisite ? 'site_root_id' : 'id'
            ];
        }
    }

    /**
     * getSourceBlueprint validates and converts source to a blueprint
     */
    protected function getSourceBlueprint()
    {
        if ($this->sourceCache !== null) {
            return $this->sourceCache;
        }

        if (!$this->source) {
            throw new SystemException("Missing source for '{$this->fieldName}'.");
        }

        $indexer = BlueprintIndexer::instance();

        $uuid = $indexer->hasSection($this->source);
        if (!$uuid) {
            throw new SystemException("Invalid source '{$this->source}' for '{$this->fieldName}'.");
        }

        return $this->sourceCache = BlueprintIndexer::instance()->findSection($uuid);
    }

    /**
     * getSourceFieldset returns the source fieldset definition for checking inverse relations
     */
    protected function getSourceFieldset()
    {
        if ($this->fieldsetCache !== null) {
            return $this->fieldsetCache;
        }

        return $this->fieldsetCache = BlueprintIndexer::instance()->findContentFieldset($this->getSourceBlueprint()->uuid);
    }
}
