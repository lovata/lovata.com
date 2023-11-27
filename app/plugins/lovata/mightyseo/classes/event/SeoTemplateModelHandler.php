<?php namespace Lovata\MightySeo\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\MightySeo\Models\SeoTemplate;
use Lovata\MightySeo\Classes\Item\SeoTemplateItem;

/**
 * Class SeoTemplateModelHandler
 * @package Lovata\MightySeo\Classes\Event
 * @author  Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SeoTemplateModelHandler extends ModelHandler
{
    protected $sIdentifierField = 'key';

    /** @var  SeoTemplate */
    protected $obElement;

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return SeoTemplate::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return SeoTemplateItem::class;
    }
}
