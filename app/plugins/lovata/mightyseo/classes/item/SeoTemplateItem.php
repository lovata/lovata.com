<?php namespace Lovata\MightySeo\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\MightySeo\Models\SeoTemplate;

/**
 * Class TemplateItem
 * @package Lovata\MightySeo\Classes\Item
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int $id
 * @property string $key
 * @property string $value
 */
class SeoTemplateItem extends ElementItem
{
    const MODEL_CLASS = SeoTemplate::class;

    /** @var SeoTemplate */
    protected $obElement = null;

    /**
     * Set element object
     */
    protected function setElementObject()
    {
        $this->obElement = SeoTemplate::getByKey($this->iElementID)->first();
    }
}
