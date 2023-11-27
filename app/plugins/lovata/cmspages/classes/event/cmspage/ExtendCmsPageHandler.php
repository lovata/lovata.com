<?php namespace Lovata\CmsPages\Classes\Event\CmsPage;

use Event;

/**
 * Class ExtendCmsPageHandler
 * @package Lovata\CmsPages\Classes\Event\CmsPage
 * @author  Igor Tverdokhleb, i.tverdokhleb@lovata.com, Lovata sp. z o.o.
 */
class ExtendCmsPageHandler
{
    public function subscribe(): void
    {
        Event::listen('cms.template.extendTemplateSettingsFields', function ($extension, $dataHolder) {
            if ($dataHolder->templateType === 'page') {
                $dataHolder->settings[] = [
                    'property' => 'meta_keywords',
                    'title' => 'lovata.cmspages::lang.field.meta_keywords',
                    'type' => 'text',
                    'size' => 'medium',
                    'tab' => 'cms::lang.editor.meta',
                ];
            }
        });
    }
}
