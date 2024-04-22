<?php namespace Tailor\Models;

use Site;
use October\Contracts\Element\ListElement;
use October\Contracts\Element\FormElement;
use October\Contracts\Element\FilterElement;
use Tailor\Classes\BlueprintModel;
use Tailor\Classes\BlueprintIndexer;
use Tailor\Classes\Scopes\EntryRecordScope;
use SystemException;

/**
 * EntryRecord model for content
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class EntryRecord extends BlueprintModel
{
    use \Tailor\Traits\DraftableModel;
    use \Tailor\Traits\NestedTreeModel;
    use \Tailor\Traits\VersionableModel;
    use \Tailor\Traits\DeferredContentModel;
    use \Tailor\Models\EntryRecord\HasDuplication;
    use \Tailor\Models\EntryRecord\HasStatusScopes;
    use \Tailor\Models\EntryRecord\HasEntryBlueprint;
    use \October\Rain\Database\Traits\Multisite;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var array rules for validation
     */
    public $rules = [
        'title' => 'required'
    ];

    /**
     * @var array fillable fields, in addition to those dynamically added by content fields
     */
    protected $fillable = [
        'title'
    ];

    /**
     * @var array attributeNames of custom attributes
     */
    public $attributeNames = [];

    /**
     * @var array customMessages of custom error messages
     */
    public $customMessages = [];

    /**
     * @var string contentGroupFrom attribute
     */
    public $contentGroupFrom = 'content_group';

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = ['published_at', 'published_at_date', 'expired_at'];

    /**
     * @var array propagatable list of attributes to propagate to other sites.
     */
    protected $propagatable = [];

    /**
     * @var array fieldModifiers are attributes that are hard coded by the model
     * but can still extend the model using their content fields.
     */
    public $fieldModifiers = [
        'id',
        'title',
        'slug',
        'fullslug',
        'entry_type_name',
        'published_at_date',
        'status_code',
        'is_enabled',
        'published_at',
        'expired_at',
        'parent_id',
        'draft_mode'
    ];

    /**
     * defineListColumns
     */
    public function defineListColumns(ListElement $host)
    {
        $host->defineColumn('id', 'ID')->invisible();
        $host->defineColumn('title', 'Title')->searchable(true);
        $host->defineColumn('slug', 'Slug')->searchable(true)->invisible();

        if ($this->isEntryStructure()) {
            $host->defineColumn('fullslug', 'Full Slug')->searchable(false)->invisible();
        }

        $host->defineColumn('entry_type_name', 'Entry Type')->shortLabel('Type')->invisible()->sortable(false);

        $this->getContentFieldsetDefinition()->defineAllListColumns($host, ['except' => $this->fieldModifiers]);

        $host->defineColumn('published_at_date', 'Published Date')->shortLabel('Published')->displayAs('date')->invisible(!$this->isEntryStream())->sortableDefault($this->isEntryStream() ? 'desc' : false);
        $host->defineColumn('status_code', 'Status')->shortLabel('')->displayAs('selectable')->sortable(false)->align('right');
        $this->applyCoreColumnModifiers($host);
    }

    /**
     * defineFilterScopes
     */
    public function defineFilterScopes(FilterElement $host)
    {
        $host->defineScope('status_code', 'Status')->displayAs('dropdown')->options('getStatusCodeOptions')->emptyOption('All Entries')->modelScope('applyStatusFromFilter');

        $this->getContentFieldsetDefinition()->defineAllFilterScopes($host, ['except' => $this->fieldModifiers]);

        $host->defineScope('published_at_date', 'Published Date')->displayAs('date');
    }

    /**
     * defineFormFields
     */
    public function defineFormFields(FormElement $host)
    {
        $entryName = $this->getContentFieldsetDefinition()->name ?? '';

        $host->addFormField('title', 'Title')->autoFocus()->cssClass('primary-title-field')->placeholder(__("Create :name Entry", ['name' => __($entryName)]));
        $this->applyCoreFieldModifiers($host);
    }

    /**
     * definePrimaryFormFields
     */
    public function definePrimaryFormFields(FormElement $host)
    {
        $this->getFieldsetDefinition()->defineAllFormFields($host, [
            'except' => $this->fieldModifiers,
            'context' => $host->getFormContext()
        ]);
    }

    /**
     * defineSecondaryFormFields
     */
    public function defineSecondaryFormFields(FormElement $host)
    {
        $host->addFormField('slug', 'Slug')->preset(['field' => 'title', 'type' => 'slug']);
        $host->addFormField('is_enabled', 'Enabled')->displayAs('switch')->defaults(true);
        $host->addFormField('published_at', 'Publish Date')->displayAs('datepicker')->defaultTimeMidnight();
        $host->addFormField('expired_at', 'Expiry Date')->displayAs('datepicker')->defaultTimeMidnight();
        $host->addFormField('parent_id', 'Parent')->displayAs('dropdown');
        $this->applyCoreFieldModifiers($host);
    }

    /**
     * applyCoreFieldModifiers will transfer modified attributes from the blueprint
     * to the core field definition. For example, the title placeholder value or
     * the is_enabled default state.
     */
    protected function applyCoreFieldModifiers(FormElement $host)
    {
        $toTransfer = [
            'scope',
            'column',
            'default',
            'label',
            'comment',
            'commentAbove',
            'commentHtml',
            'placeholder',
            'readOnly',
            'hidden'
        ];

        $fieldset = $this->getFieldsetDefinition();
        $formFields = $host->getFormFieldset();

        foreach ($formFields->getAllFields() as $name => $field) {
            if ($modifier = $fieldset->getField($name)) {
                $field->useConfig(array_only($modifier->getConfig(), $toTransfer));
            }

            // Remove required validation for title field
            if ($name === 'title' && $field->hidden) {
                unset($this->rules['title']);
            }
        }
    }

    /**
     * applyCoreColumnModifiers will transfer modified attributes from the blueprint
     * to the core column definition. For example, the title column.
     */
    protected function applyCoreColumnModifiers(ListElement $host)
    {
        $coreColumns = [
            'id',
            'title',
            'slug',
            'fullslug',
            'entry_type_name',
            'published_at_date',
            'status_code',
        ];

        $toTransfer = [
            'label',
            'shortLabel',
            'valueFrom',
            'invisible',
            'hidden'
        ];

        $fieldset = $this->getFieldsetDefinition();
        $listColumns = $host->getColumns();

        foreach ($coreColumns as $columnName) {
            $column = $listColumns[$columnName] ?? null;
            $field = $fieldset->getField($columnName);
            if (!$column || !$field) {
                continue;
            }

            $modifier = $field->column;
            if (is_array($modifier)) {
                $column->useConfig(array_only($modifier, $toTransfer));
            }
            elseif (is_string($modifier)) {
                $column->$modifier();
            }
            elseif ($field->hidden || $modifier === false) {
                $column->hidden();
            }
        }
    }

    /**
     * afterBoot
     */
    public function afterBoot()
    {
        static::addGlobalScope(new EntryRecordScope);
    }

    /**
     * beforeUpdate
     */
    public function beforeUpdate()
    {
        $this->setPublishingDates($this->published_at ?: $this->created_at);
    }

    /**
     * beforeCreate
     */
    public function beforeCreate()
    {
        $this->setPublishingDates($this->published_at ?: $this->freshTimestamp());
    }

    /**
     * setPublishingDates
     */
    protected function setPublishingDates($useDate)
    {
        if ($this->isEntryStream()) {
            $this->published_at_day = $useDate->format('d');
            $this->published_at_month = $useDate->format('m');
            $this->published_at_year = $useDate->format('Y');
        }

        $this->published_at_date = $useDate;
    }

    /**
     * inSection
     */
    public static function inSection($handle)
    {
        $blueprint = BlueprintIndexer::instance()->findSectionByHandle($handle);
        if (!$blueprint) {
            throw new SystemException("Section handle [{$handle}] not found");
        }

        return static::inSectionUuid($blueprint->uuid);
    }

    /**
     * inSectionUuid
     */
    public static function inSectionUuid($uuid)
    {
        $instance = new static;

        $instance->extendWithBlueprint($uuid);

        return $instance;
    }

    /**
     * extendInSection
     */
    public static function extendInSection($handle, callable $callback)
    {
        $blueprint = BlueprintIndexer::instance()->findSectionByHandle($handle);
        if (!$blueprint) {
            throw new SystemException("Section handle [{$handle}] not found");
        }

        self::extendInSectionUuid($blueprint->uuid, $callback);
    }

    /**
     * extendInSectionUuid
     */
    public static function extendInSectionUuid($uuid, callable $callback)
    {
        static::extend(function($model) use ($uuid, $callback) {
            $model->bindEvent('model.extendBlueprint', function($foundUuid) use ($uuid, $callback, $model) {
                if ($uuid === $foundUuid) {
                    $callback($model);
                }
            });
        });
    }

    /**
     * scopeApplyVisibleFrontend adds visibility to the front end with support
     * for preview tokens
     */
    public function scopeApplyVisibleFrontend($query)
    {
        if (PreviewToken::isTokenEnabled()) {
            return $query;
        }

        return $query->applyPublishedStatus();
    }

    /**
     * getEntryTypeNameAttribute
     */
    public function getEntryTypeNameAttribute()
    {
        return BlueprintIndexer::instance()
            ->findFieldset($this->blueprint_uuid, $this->content_group)
            ->name ?? '';
        ;
    }

    /**
     * getEntryTypeAttribute
     */
    public function getEntryTypeAttribute()
    {
        return BlueprintIndexer::instance()
            ->findFieldset($this->blueprint_uuid, $this->content_group)
            ->handle ?? '';
        ;
    }

    /**
     * setDefaultContentGroup populate the default content group for new records
     */
    public function setDefaultContentGroup($defaultGroup = null)
    {
        $groupOptions = $this->getContentGroupOptions();

        if (!isset($groupOptions[$defaultGroup])) {
            $defaultGroup = array_keys($groupOptions)[0] ?? null;
        }

        if ($defaultGroup) {
            $this->setBlueprintGroup($defaultGroup);
        }
    }

    /**
     * getContentGroupOptions
     */
    public function getContentGroupOptions()
    {
        return $this->getBlueprintDefinition()->getEntryTypeOptions();
    }

    /**
     * getParentIdOptions
     */
    public function getParentIdOptions()
    {
        return $this->getParentIdOptionsFromQuery($this->withSavedDrafts());
    }

    /**
     * findSingleForSection
     */
    public static function findSingleForSection($handle): EntryRecord
    {
        $blueprint = BlueprintIndexer::instance()->findSectionByHandle($handle);
        if (!$blueprint) {
            throw new SystemException("Section handle [{$handle}] not found");
        }

        return static::findSingleForSectionUuid($blueprint->uuid);
    }

    /**
     * findSingleForSectionUuid
     */
    public static function findSingleForSectionUuid($uuid): EntryRecord
    {
        // Find existing record
        $record = static::inSectionUuid($uuid)->first();
        if ($record) {
            return $record;
        }

        // Create new record
        $entry = new static;
        $entry->extendWithBlueprint($uuid);
        $entry->is_enabled = true;
        $entry->forceSave();

        return $entry;
    }

    /**
     * useNestedTreeStructure only for primary records
     */
    public function useNestedTreeStructure(): bool
    {
        return $this->isEntryStructure() && !$this->primary_id;
    }

    /**
     * newNestedTreeQuery creates a new query for nested sets
     */
    protected function newNestedTreeQuery()
    {
        $query = $this->newQuery()->withSavedDrafts();

        // Nested tree query must have context
        if (Site::hasGlobalContext() && $this->isMultisiteEnabled()) {
            $query->withSite(Site::getSiteIdFromContext());
        }

        return $query;
    }

    /**
     * getVersionableTransferAttributes
     */
    protected function getVersionableTransferAttributes()
    {
        return $this->getFieldsetColumnNames();
    }

    /**
     * getMorphClass adds dynamic table support
     * @return string
     */
    public function getMorphClass()
    {
        return parent::getMorphClass() . '@' . $this->getTable();
    }

    /**
     * isMultisiteEnabled allows for programmatic toggling
     * @return bool
     */
    public function isMultisiteEnabled()
    {
        return $this->useMultisite();
    }

    /**
     * isMultisiteSyncEnabled
     * @return bool
     */
    public function isMultisiteSyncEnabled()
    {
        return $this->useMultisiteSync();
    }

    /**
     * getMultisiteConfig returns an sync option configured for multisite
     */
    public function getMultisiteConfig($key, $default = null)
    {
        return $this->getBlueprintDefinition()->getMultisiteConfig($key, $default);
    }

    /**
     * makePageUrlParams returns parameters used when linking to this record as a page
     */
    public function makePageUrlParams(): array
    {
        $replacements = parent::makePageUrlParams();

        // Use context for correct relations
        Site::withContext($this->site_id, function() use (&$replacements) {
            $wantReplace = $this->getBlueprintDefinition()->getPageFinderReplacements();

            foreach ($wantReplace as $key => $path) {
                $replacements[$key] = array_get($this, $path);
            }
        });

        return $replacements;
    }
}
