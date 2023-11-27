<?php namespace Tailor\Classes;

use System\Classes\ModelBehavior;
use SystemException;

/**
 * ModelBehaviorBase class
 */
class ModelBehaviorBase extends ModelBehavior
{
    /**
     * @var bool isBlueprintExtended prevents multiple extensions
     */
    protected $isBlueprintExtended = false;

    /**
     * extendWithBlueprint
     */
    public function extendWithBlueprint(string $uuid = null)
    {
        if ($this->isBlueprintExtended) {
            return;
        }

        if ($uuid !== null) {
            $this->setBlueprintUuid($uuid);
        }

        $uuid = $this->model->blueprint_uuid;
        if ($uuid === null) {
            return;
        }

        $this->model->setTable($this->model->getBlueprintDefinition()->getContentTableName());

        $this->getContentFieldsetDefinition()->applyModelExtensions($this->model);

        $this->isBlueprintExtended = true;

        /**
         * @event model.extendBlueprint
         * Called when the model is extended with the blueprint, called once per model instance
         *
         * Example usage:
         *
         *     $model->bindEvent('model.extendBlueprint', function (string $uuid) use (\October\Rain\Database\Model $model) {
         *         // Apply specific uuid actions
         *         $model->add_status_to_history = true;
         *     });
         *
         */
        $this->model->fireEvent('model.extendBlueprint', [$uuid]);
    }

    /**
     * getBlueprintUuid
     */
    public function getBlueprintUuid(): string
    {
        $uuid = $this->model->blueprint_uuid;
        if (!$uuid) {
            throw new SystemException("Missing content UUID. Set the attribute 'blueprint_uuid' on the model first.");
        }

        return $uuid;
    }

    /**
     * setBlueprintUuid
     */
    public function setBlueprintUuid($value)
    {
        $this->model->blueprint_uuid = $value;
    }

    /**
     * getBlueprintGroupAttribute
     */
    public function getBlueprintGroupAttribute(): ?string
    {
        return $this->model->contentGroupFrom ?? null;
    }

    /**
     * getBlueprintGroup
     */
    public function getBlueprintGroup(): ?string
    {
        $groupAttr = $this->getBlueprintGroupAttribute();

        if ($groupAttr === null) {
            return null;
        }

        $group = $this->model->$groupAttr;
        if (!$group) {
            return null;
        }

        return $group;
    }

    /**
     * setBlueprintGroup
     */
    public function setBlueprintGroup($value): void
    {
        $groupAttr = $this->getBlueprintGroupAttribute();

        if ($groupAttr !== null) {
            $this->model->$groupAttr = $value;
        }
    }

    /**
     * getFieldsetColumnNames
     */
    public function getFieldsetColumnNames(): array
    {
        return $this->getContentFieldsetDefinition()->getContentColumnNames();
    }

    /**
     * getFieldsetDefinition
     */
    public function getFieldsetDefinition(): Fieldset
    {
        $fieldset = BlueprintIndexer::instance()->findFieldset(
            $this->getBlueprintUuid(),
            $this->getBlueprintGroup()
        );

        if (!$fieldset) {
            $fieldset = $this->getContentFieldsetDefinition();
        }

        return $fieldset;
    }

    /**
     * getContentFieldsetDefinition
     */
    public function getContentFieldsetDefinition(): Fieldset
    {
        $uuid = $this->getBlueprintUuid();

        $fieldset = BlueprintIndexer::instance()->findContentFieldset($uuid);

        if (!$fieldset) {
            throw new SystemException("Unable to find content fieldset definition with UUID of '{$uuid}'.");
        }

        return $fieldset;
    }
}
