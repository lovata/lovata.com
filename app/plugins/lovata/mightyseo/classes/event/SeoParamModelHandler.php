<?php namespace Lovata\MightySeo\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\MightySeo\Models\SeoParam;
use Lovata\MightySeo\Classes\Item\SeoParamItem;

/**
 * Class SeoParamModelHandler
 * @package Lovata\MightySeo\Classes\Event
 * @author  Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SeoParamModelHandler extends ModelHandler
{
    /** @var  SeoParam */
    protected $obElement;

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return SeoParam::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return SeoParamItem::class;
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();
        if (empty($this->obElement->page_id)) {
            return;
        }

        SeoParamItem::clearCache($this->obElement->page_id);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();
        if (empty($this->obElement->page_id)) {
            return;
        }

        SeoParamItem::clearCache($this->obElement->page_id);
    }
}
