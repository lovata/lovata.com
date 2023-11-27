<?php namespace Lovata\BaseCode\Classes\Store\Project;

use Lovata\BaseCode\Models\Project;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

/**
 * Class SortingListStore
 * @package Lovata\BaseCode\Classes\Store\Project
 * @author  Sergey Zakharevich, <s.v.zakharevich@gmail.com>, LOVATA Group
 */
class SortingListStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) Project::orderBy('date', 'desc')->lists('id');

        return $arElementIDList;
    }
}
