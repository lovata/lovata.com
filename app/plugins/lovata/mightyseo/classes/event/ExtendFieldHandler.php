<?php namespace Lovata\MightySeo\Classes\Event;

use Lovata\MightySeo\Models\SeoParam;
use Lovata\MightySeo\Models\SeoTemplate;
use Lovata\MightySeo\Controllers\SeoPageParams;
use Lovata\MightySeo\Classes\Helper\SeoConfigHelper;

/**
 * Class ExtendFieldHandler
 * @package Lovata\MightySeo\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendFieldHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $this->extendSeoParamBackendFields($obEvent);
        $this->extendSeoTemplateBackendFields($obEvent);
    }

    /**
     * Add SEO fields to backend forms
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    protected function extendSeoParamBackendFields($obEvent)
    {
        $obEvent->listen('backend.form.extendFields', function ($obWidget) {

            /** @var \Backend\Widgets\Form $obWidget */
            if (!$obWidget->model instanceof SeoParam || !$obWidget->getController() instanceof SeoPageParams) {
                return;
            }

            if ($obWidget->isNested || preg_match('%PivotForm$%', $obWidget->alias)) {
                return;
            }

            SeoConfigHelper::addSeoParamField($obWidget);
        });
    }

    /**
     * Add value fields to backend forms for SeoTemplate model
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    protected function extendSeoTemplateBackendFields($obEvent)
    {
        $obEvent->listen('backend.form.extendFields', function ($obWidget) {

            /** @var \Backend\Widgets\Form $obWidget */
            if (!$obWidget->model instanceof SeoTemplate || $obWidget->isNested) {
                return;
            }

            $arFieldList = SeoConfigHelper::getSeoTemplateFieldList();
            if (empty($arFieldList)) {
                return;
            }

            $obWidget->addFields($arFieldList);
        });
    }
}
