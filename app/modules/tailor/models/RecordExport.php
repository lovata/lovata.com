<?php namespace Tailor\Models;

use Backend\Models\ExportModel;
use October\Contracts\Element\ListElement;
use October\Contracts\Element\FormElement;

/**
 * RecordExport for exporting records (entries or globals)
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class RecordExport extends ExportModel
{
    use \Tailor\Models\RecordImport\HasGeneralBlueprint;

    /**
     * defineListColumns
     */
    public function defineListColumns(ListElement $host)
    {
        $host->defineColumn('id', 'ID');
        $host->defineColumn('title', 'Title');
        $host->defineColumn('slug', 'Slug');
        $host->defineColumn('is_enabled', 'Enabled');
        $host->defineColumn('published_at', 'Publish Date');
        $host->defineColumn('expired_at', 'Expiry Date');
        $host->defineColumn('content_group', 'Entry Type');

        if ($this->isEntryStructure()) {
            $host->defineColumn('parent_id', 'Parent');
        }

        $this->getContentFieldsetDefinition()->defineAllListColumns($host, ['context' => 'export']);
    }

    /**
     * defineFormFields
     */
    public function defineFormFields(FormElement $host)
    {
    }

    /**
     * exportData
     */
    public function exportData($columns, $sessionKey = null)
    {
        $records = $this->resolveBlueprintModel()->get();
        $result = [];

        foreach ($records as $record) {
            $item = [];
            foreach ($columns as $column) {
                $item[$column] = $this->encodeModelAttribute($record, $column);
            }
            $result[] = $item;
        }

        return $result;
    }

    /**
     * encodeModelAttribute
     */
    protected function encodeModelAttribute($model, $attr)
    {
        if ($model->hasRelation($attr)) {
            $relationModel = $model->makeRelation($attr);
            if ($relationModel instanceof RepeaterItem) {
                $value = $this->encodeRepeaterItems($model, $attr);
            }
            else {
                $value = $model->getRelationValue($attr);
            }
        }
        else {
            $value = $model->{$attr};
        }

        if (is_array($value)) {
            $value = $this->encodeArrayValue($value);
        }

        if ($value instanceof \DateTimeInterface) {
            $value = $value->format($model->getDateFormat());
        }

        return $value;
    }

    /**
     * encodeRepeaterItems
     */
    protected function encodeRepeaterItems($model, $attr)
    {
        if ($model->isRelationTypeSingular($attr)) {
            return $this->encodeRepeaterItem($model->{$attr});
        }

        $result = [];

        foreach ($model->{$attr} as $item) {
            $result[] = $this->encodeRepeaterItem($item);
        }

        return $result;
    }

    /**
     * encodeRepeaterItem
     */
    protected function encodeRepeaterItem($item)
    {
        // Locate attribute and relation names
        $attrs = array_keys($item->attributes);
        $definitions = $item->getRelationDefinitions();
        foreach ($definitions as $type => $relations) {
            if (in_array($type, ['morphTo'])) {
                continue;
            }

            foreach ($relations as $name => $options) {
                $attrs[] = $name;
            }
        }

        // Excluded values
        $exclude = [
            'id',
            'host_id',
            'host_field',
            'content_value',
            'content_spawn_path'
        ];

        // Encode values
        $values = [];

        foreach ($attrs as $attr) {
            if (in_array($attr, $exclude)) {
                continue;
            }

            $values[$attr] = $this->encodeModelAttribute($item, $attr);
        }

        return $values;
    }
}
