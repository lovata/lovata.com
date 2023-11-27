<?php namespace Tailor\Traits;

use Tailor\Classes\Scopes\VersionableScope;

/**
 * VersionableModel trait allows version versions of models
 *
 * @property int $primary_id
 * @property array $primary_attrs
 * @property \October\Rain\Database\Model|null $primaryRecord
 * @property \October\Rain\Database\Collection|null $versions
 * @method \October\Rain\Database\Relations\HasMany versions()
 * @method \October\Rain\Database\Relations\BelongsTo primaryRecord()
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait VersionableModel
{
    /**
     * bootVersionableModel trait for a model.
     */
    public static function bootVersionableModel()
    {
        static::addGlobalScope(new VersionableScope);
    }

    /**
     * initializeVersionableModel
     */
    public function initializeVersionableModel()
    {
        if (!$this->isJsonable('primary_attrs')) {
            $this->addJsonable('primary_attrs');
        }

        $this->belongsTo['primaryRecord'] = [
            static::class,
            'key' => 'primary_id',
            'scope' => 'withDrafts',
            'replicate' => false
        ];

        $this->hasMany['versions'] = [
            static::class,
            'key' => 'primary_id',
            'replicate' => false
        ];

        $this->bindEvent('model.afterRelation', function($name, $related) {
            if (in_array($name, ['versions', 'primaryRecord'])) {
                $related->extendWithBlueprint($this->blueprint_uuid);
            }
        });
    }

    /**
     * getVersionRecords
     */
    public function getVersionRecords()
    {
        if ($this->primary_id) {
            return $this->primaryRecord->versions;
        }
        else {
            return $this->versions;
        }
    }

    /**
     * getVersionableTransferAttributes override method
     */
    protected function getVersionableTransferAttributes()
    {
        return [];
    }

    /**
     * countVersions will return the number of available versions.
     */
    public function countVersions(): int
    {
        return $this->versions()->count();
    }

    /**
     * saveVersionSnapshot
     */
    public function saveVersionSnapshot(array $attrs = [])
    {
        $model = $this->newInstance();

        foreach ($this->getVersionableTransferAttributes() as $attr) {
            $model->$attr = $this->$attr;
        }

        $model->{$this->getIsVersionColumn()} = true;

        $model->primary_id = $this->getKey();

        $model->primary_attrs = $attrs;

        $model->save(['force' => true]);

        return $model;
    }

    /**
     * restoreVersionSnapshot
     */
    public function restoreVersionSnapshot($toModel)
    {
        $toModel->saveVersionSnapshot();

        foreach ($this->getVersionableTransferAttributes() as $attr) {
            $toModel->$attr = $this->$attr;
        }

        $toModel->save(['force' => true]);
    }

    /**
     * isVersionStatus
     */
    public function isVersionStatus(): bool
    {
        return (bool) $this->{$this->getIsVersionColumn()};
    }

    /**
     * getIsVersionColumn gets the name of the "is_version" column.
     */
    public function getIsVersionColumn(): string
    {
        return 'is_version';
    }

    /**
     * getQualifiedIsVersionColumn gets the fully qualified "is_version" column.
     */
    public function getQualifiedIsVersionColumn(): string
    {
        return $this->getTable().'.'.$this->getIsVersionColumn();
    }
}
