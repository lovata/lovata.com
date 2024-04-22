<?php namespace System\Models\SettingModel;

/**
 * HasMultisite concern
 */
trait HasMultisite
{
    /**
     * initializeHasMultisite trait for a model.
     */
    public function initializeHasMultisite()
    {
        if (!$this->isClassInstanceOf(\October\Contracts\Database\MultisiteInterface::class)) {
            return;
        }

        $this->bindEvent('model.beforeSave', [$this, 'settingMultisiteBeforeSave']);
        $this->bindEvent('model.initSettingsData', [$this, 'settingMultisiteInitSettingsData']);
    }

    /**
     * settingMultisiteBeforeSave
     */
    public function settingMultisiteBeforeSave()
    {
        if ($this->site_root_id) {
            return;
        }

        $otherModel = $this->findOtherSettingModel();
        if (!$otherModel) {
            return;
        }

        $this->site_root_id = $otherModel->site_root_id ?: $otherModel->id;
    }

    /**
     * settingMultisiteInitSettingsData
     */
    public function settingMultisiteInitSettingsData()
    {
        if (!$this->isMultisiteEnabled() || !$this->propagatable) {
            return;
        }

        $otherModel = $this->findOtherSettingModel();
        if (!$otherModel) {
            return;
        }

        // Reversed logic taken from Multisite trait, propagateToSite method
        foreach ($this->propagatable as $name) {
            $relationType = $this->getRelationType($name);

            // Propagate local key relation
            if ($relationType === 'belongsTo') {
                $fkName = $this->$name()->getForeignKeyName();
                $this->$fkName = $otherModel->$fkName;
            }
            // Propagate local attribute (not a relation)
            elseif (!$relationType) {
                $this->$name = $otherModel->$name;
            }
        }
    }

    /**
     * findOtherSettingModel
     */
    public function findOtherSettingModel()
    {
        $query = $this->newQueryWithoutScopes()->where('item', $this->settingsCode);

        if ($this->exists) {
            $query = $query->where($this->getKeyName(), '<>', $this->getKey());
        }

        return $query->first();
    }
}
