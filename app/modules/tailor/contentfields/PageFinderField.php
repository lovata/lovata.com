<?php namespace Tailor\ContentFields;

use October\Contracts\Element\ListElement;

/**
 * PageFinderField is an implementation of the pagefinder form widget
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class PageFinderField extends FallbackField
{
    /**
     * defineListColumn
     */
    public function defineListColumn(ListElement $list, $context = null)
    {
        $list->defineColumn($this->fieldName, $this->label)
            ->displayAs('linkage')
            ->shortLabel($this->shortLabel)
            ->attributes([
                'target' => '_blank'
            ])
            ->useConfig($this->column ?: [])
        ;
    }
}
