<?php namespace Lovata\BaseCode\Classes\Event\CmsPage;

use Event;

class ExtendCmsPageHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Event::listen('cms.template.extendTemplateSettingsFields', function ($extension, $dataHolder) {
            if ($dataHolder->templateType === 'page') {
                $dataHolder->settings[] = [
                    'property' => 'meta_keywords',
                    'title'    => 'Meta Keywords',
                    'type'     => 'text',
                    'size'     => 'medium',
                    'tab'      => 'cms::lang.editor.meta',
                ];
            }
        });
    }
}
