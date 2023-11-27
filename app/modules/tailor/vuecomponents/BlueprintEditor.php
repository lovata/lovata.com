<?php namespace Tailor\VueComponents;

use Backend\Classes\VueComponentBase;

/**
 * BlueprintEditor Vue component
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class BlueprintEditor extends VueComponentBase
{
    /**
     * @var array require
     */
    protected $require = [
        \Backend\VueComponents\MonacoEditor::class
    ];
}
