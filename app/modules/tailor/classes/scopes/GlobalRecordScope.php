<?php namespace Tailor\Classes\Scopes;

use Illuminate\Database\Eloquent\Model as ModelBase;
use Illuminate\Database\Eloquent\Scope as ScopeInterface;
use Illuminate\Database\Eloquent\Builder as BuilderBase;

/**
 * GlobalRecordScope
 *
 * @package october\database
 * @author Alexey Bobkov, Samuel Georges
 */
class GlobalRecordScope implements ScopeInterface
{
    /**
     * apply the scope to a given Eloquent query builder.
     */
    public function apply(BuilderBase $builder, ModelBase $model)
    {
        if ($blueprintUuid = $model->blueprint_uuid) {
            $builder->where('blueprint_uuid', $blueprintUuid);
        }
    }

    /**
     * extend the Eloquent query builder.
     */
    public function extend(BuilderBase $builder)
    {
    }
}
