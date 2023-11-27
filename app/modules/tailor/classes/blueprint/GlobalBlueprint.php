<?php namespace Tailor\Classes\Blueprint;

use Tailor\Classes\Blueprint;

/**
 * GlobalBlueprint
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class GlobalBlueprint extends Blueprint
{
    /**
     * @var string typeName of the blueprint
     */
    protected $typeName = 'global';

    /**
     * makeBlueprintTableName where type can be used for content, join or repeater
     */
    protected function makeBlueprintTableName($type = 'content'): string
    {
        if ($type === 'content') {
            return 'tailor_globals';
        }

        if ($type === 'join') {
            return 'tailor_global_joins';
        }

        if ($type === 'repeater') {
            return 'tailor_global_repeaters';
        }

        return '';
    }

    /**
     * useMultisite
     */
    public function useMultisite(): bool
    {
        return (bool) $this->multisite;
    }
}
