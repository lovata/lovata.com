<?php namespace Lovata\MightySeo\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\MightySeo\Models\SeoParam;

/**
 * Class SeoParamItem
 * @package Lovata\MightySeo\Classes\Item
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int $id
 * @property string $page_id
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property string $page_h1
 * @property string $page_description
 * @property string $robots_follow
 * @property string $robots_index
 * @property string $robots_index_addition
 * @property string $canonical_url
 */
class SeoParamItem extends ElementItem
{
    const MODEL_CLASS = SeoParam::class;

    /** @var SeoParam */
    protected $obElement = null;

    /**
     * Set element object
     */
    protected function setElementObject()
    {
        $this->obElement = SeoParam::getByPage($this->iElementID)->first();
        if (empty($this->obElement)) {
            $this->obElement = SeoParam::find((int) $this->iElementID);
        }
    }
}
