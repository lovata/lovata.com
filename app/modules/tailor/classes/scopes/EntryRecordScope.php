<?php namespace Tailor\Classes\Scopes;

use Illuminate\Database\Eloquent\Model as ModelBase;
use Illuminate\Database\Eloquent\Scope as ScopeInterface;
use Illuminate\Database\Eloquent\Builder as BuilderBase;

/**
 * EntryRecordScope
 *
 * @package october\database
 * @author Alexey Bobkov, Samuel Georges
 */
class EntryRecordScope implements ScopeInterface
{
    /**
     * apply the scope to a given Eloquent query builder.
     */
    public function apply(BuilderBase $builder, ModelBase $model)
    {
        if ($model->isEntryStructure()) {
            $builder->orderBy('nest_left');
        }

        if ($model->isEntryStream()) {
            $builder->orderBy('published_at_date', 'desc');
        }
    }

    /**
     * extend the Eloquent query builder.
     */
    public function extend(BuilderBase $builder)
    {
        $removeOnMethods = ['orderBy', 'groupBy'];

        foreach ($removeOnMethods as $method) {
            $builder->macro($method, function ($builder, ...$args) use ($method) {
                $builder
                    ->withoutGlobalScope($this)
                    ->getQuery()
                    ->$method(...$args)
                ;

                return $builder;
            });
        }
    }
}
