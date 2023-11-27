<?php namespace Lovata\BaseCode\Classes\Collection;

use Lovata\BaseCode\Classes\Item\ProjectItem;
use Lovata\Toolbox\Classes\Collection\ElementCollection;
use Lovata\BaseCode\Classes\Store\ProjectListStore;

/**
 * Class ProjectCollection
 * @package Lovata\BaseCode\Classes\Item
 * @author Sergey Zakharevich, <s.v.zakharevich@gmail.com>, LOVATA Group
 */
class ProjectCollection extends ElementCollection
{
    const ITEM_CLASS = ProjectItem::class;

    /**
     * Apply filter by active project list
     * @return $this
     */
    public function active()
    {
        $arResultIDList = ProjectListStore::instance()->active->get();

        return $this->intersect($arResultIDList);
    }

    /**
     * Sort list by
     * @return $this
     */
    public function sort()
    {
        $arResultIDList = ProjectListStore::instance()->sorting->get();

        return $this->applySorting($arResultIDList);
    }

    /**
     * Filter project list by category ID
     * @param integer $iCategoryId
     * @return $this
     */
    public function category($iCategoryId)
    {
        $arResultIDList = ProjectListStore::instance()->category->get($iCategoryId);

        return $this->intersect($arResultIDList);
    }
}
