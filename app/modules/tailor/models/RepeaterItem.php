<?php namespace Tailor\Models;

use Tailor\Classes\Fieldset;
use Tailor\Classes\FieldManager;
use October\Contracts\Element\FormElement;
use October\Rain\Database\ExpandoModel;
use SystemException;

/**
 * RepeaterItem stores generic content serialized as JSON
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class RepeaterItem extends ExpandoModel
{
    use \Tailor\Traits\DeferredContentModel;
    use \October\Rain\Database\Traits\Sortable;
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var array rules for validation
     */
    public $rules = [];

    /**
     * @var array fillable fields, in addition to those dynamically added by content fields
     */
    protected $fillable = [];

    /**
     * @var string expandoColumn name to store the data
     */
    protected $expandoColumn = 'content_value';

    /**
     * @var array expandoPassthru attributes that should not be serialized
     */
    protected $expandoPassthru = [
        'content_group',
        'content_spawn_path',
        'host_id',
        'host_type',
        'host_field',
        'sort_order',
        'site_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array fieldsetConfig
     */
    protected $fieldsetConfig;

    /**
     * @var bool useFieldsetGroups
     */
    protected $useFieldsetGroups;

    /**
     * @var bool isBlueprintExtended prevents multiple extensions
     */
    protected $isBlueprintExtended = false;

    /**
     * @var array isLazyLoadedRelation
     */
    protected $isLazyLoadedRelation = [];

    /**
     * morphTo
     */
    public $morphTo = [
        'host' => []
    ];

    /**
     * getTypeAttribute
     */
    public function getTypeAttribute()
    {
        return $this->content_group;
    }

    /**
     * defineFormFields
     */
    public function defineFormFields(FormElement $host)
    {
        $this->getFieldsetDefinition()->defineAllFormFields($host);
    }

    /**
     * afterFetch
     */
    protected function afterFetch()
    {
        if ($this->fieldsetConfig) {
            $this->extendWithBlueprint();
        }
        else {
            $this->extendWithBlueprintSpawn($this->content_spawn_path);
        }
    }

    /**
     * beforeValidate
     */
    protected function beforeValidate()
    {
        // Ensure the model is extended for new records, this is delayed to the
        // last moment since the fields may depend on a content group being set
        if (!$this->exists) {
            $this->extendWithBlueprint();
        }
    }

    /**
     * beforeSave
     */
    protected function beforeSave()
    {
        if (!$this->content_spawn_path) {
            $this->content_spawn_path = $this->buildSpawnPath();
        }
    }

    /**
     * beforeReplicate make sure that the model is extended
     */
    protected function beforeReplicate()
    {
        $this->extendWithBlueprint();
    }

    /**
     * extendWithBlueprint
     */
    public function extendWithBlueprint()
    {
        if ($this->isBlueprintExtended) {
            return;
        }

        $this->getFieldsetDefinition()->applyModelExtensions($this);

        $this->isBlueprintExtended = true;
    }

    /**
     * extendWithBlueprintSpawn attempts to load from a respawned model, then copies the fieldset
     * definition across before extending the model.
     */
    public function extendWithBlueprintSpawn(string $spawnPath)
    {
        $model = $this->spawnFromPath($spawnPath);

        if (!$model) {
            throw new SystemException("Could not spawn from path [{$spawnPath}]");
        }

        $this->setFieldsetDefinition($model->getTable(), $model->fieldsetConfig, $model->useFieldsetGroups);

        if ($model->useFieldsetGroups) {
            $this->content_group = $model->content_group;
        }

        $this->extendWithBlueprint();
    }

    /**
     * setBlueprintFieldConfig
     */
    public function setBlueprintFieldConfig($parentModel, string $tableName, string $fieldName, array $fieldConfig, bool $useGroups)
    {
        $this->host_field = $fieldName;
        $this->setRelation('host', $parentModel);
        $this->setFieldsetDefinition($tableName, $fieldConfig, $useGroups);

        // Extend model now since the fields are static
        if (!$useGroups) {
            $this->extendWithBlueprint();
        }

        // Recursive implementation
        $this->bindEvent('model.newInstance', function($instance) use ($parentModel, $tableName, $fieldName, $fieldConfig, $useGroups) {
            $instance->setBlueprintFieldConfig($parentModel, $tableName, $fieldName, $fieldConfig, $useGroups);
        });
    }

    /**
     * getFieldsetDefinition returns a fieldset for the selected content group.
     */
    protected function getFieldsetDefinition(): Fieldset
    {
        $config = $this->fieldsetConfig;

        if ($this->useFieldsetGroups) {
            $config = $config[$this->content_group] ?? array_first($config);
        }

        $fieldset = FieldManager::instance()->makeFieldset($config);

        $fieldset->validate();

        return $fieldset;
    }

    /**
     * getContentFieldsetDefinition returns a merged fieldset for all groups
     * defined by the repeater item.
     */
    protected function getContentFieldsetDefinition(): Fieldset
    {
        if (!$this->useFieldsetGroups) {
            return $this->getFieldsetDefinition();
        }

        $config = null;
        foreach ($this->fieldsetConfig as $code => $attributes) {
            if ($config === null) {
                $config = $attributes;
            }
            else {
                $config['fields'] += $attributes['fields'] ?? [];
            }
        }

        return FieldManager::instance()->makeFieldset((array) $config);
    }

    /**
     * extendWithLazyLoadedRelation
     */
    protected function extendWithLazyLoadedRelation(string $name)
    {
        // No config to use, or relations already defined by extension
        if (!$this->fieldsetConfig || $this->isBlueprintExtended) {
            return;
        }

        // Check already performed once before
        if (isset($this->isLazyLoadedRelation[$name])) {
            return;
        }

        $this->getContentFieldsetDefinition()->getField($name)?->extendModel($this);

        $this->isLazyLoadedRelation[$name] = true;
    }

    /**
     * setFieldsetDefinition
     */
    public function setFieldsetDefinition(string $tableName, array $fields, bool $useGroups): void
    {
        $this->setTable($tableName);

        $this->fieldsetConfig = $fields;

        $this->useFieldsetGroups = $useGroups;
    }

    /**
     * buildSpawnPath returns a string that can be used to respawn this related model
     * from the parent. The syntax is: class@uuid:group.relation:group.relation:group
     */
    protected function buildSpawnPath()
    {
        $host = $this->host;
        $chain = $this->host_field;
        if ($this->content_group) {
            $chain = $chain.':'.$this->content_group;
        }

        // Build child relations
        if ($host instanceof self) {
            $chain = $host->buildSpawnPath().'.'.$chain;
        }
        // Build parent model
        else {
            $primaryChain = get_class($host).'@'.$host->getBlueprintUuid();

            if ($group = $host->getBlueprintGroup()) {
                $primaryChain = $primaryChain.':'.$group;
            }

            $chain = $primaryChain.'.'.$chain;
        }

        return $chain;
    }

    /**
     * spawnFromPath will respawn this related model from a saved path.
     * The syntax is: class@uuid:group.relation:group.relation:group
     */
    public static function spawnFromPath(string $path)
    {
        if (strpos($path, '@') === false) {
            return;
        }

        [$className, $parts] = explode('@', $path, 2);
        $parts = explode('.', $parts);

        if (!class_exists($className)) {
            return;
        }

        // Build parent model
        $parent = new $className;
        $parentParts = array_shift($parts);
        $parentParts = explode(':', $parentParts);

        $parent->setBlueprintUuid($parentParts[0]);
        if (isset($parentParts[1])) {
            $parent->setBlueprintGroup($parentParts[1]);
        }

        $parent->extendWithBlueprint();

        // Build child relations
        $childModel = $parent;
        foreach ($parts as $part) {
            $itemParts = explode(':', $part);
            $fieldName = $itemParts[0];
            if (!$fieldName) {
                return;
            }

            $childModel = $childModel->makeRelation($fieldName);
            if (!$childModel) {
                return;
            }

            if (isset($itemParts[1])) {
                $childModel->content_group = $itemParts[1];
            }

            $childModel->extendWithBlueprint();
        }

        return $childModel;
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
     * hasRelation magically pulls relation definitions from any group definition.
     * This method targets the __call() method to make relations available to the
     * query builder.
     */
    public function hasRelation(string $name): bool
    {
        if (parent::hasRelation($name)) {
            return true;
        }

        $this->extendWithLazyLoadedRelation($name);

        return parent::hasRelation($name);
    }
}
