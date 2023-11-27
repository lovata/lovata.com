<?php namespace Cms\Classes\Layout;

/**
 * Fields
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class Fields
{
    /**
     * defineSettingsFields
     */
    public function defineSettingsFields(): array
    {
        return [
            'is_priority' => [
                'type' => "checkbox",
                'title' => "cms::lang.editor.priority",
                'description' => "cms::lang.editor.priority_comment",
            ]
        ];
    }
}
