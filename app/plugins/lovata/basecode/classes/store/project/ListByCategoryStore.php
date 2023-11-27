<?php namespace Lovata\BaseCode\Classes\Store\Project;

use Lovata\BaseCode\Models\Project;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

/**
 * Class ListByCategoryStore
 * @package Lovata\BaseCode\Classes\Store\Project
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class ListByCategoryStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) Project::getByCategory($this->sValue)->lists('id');

        return $arElementIDList;
    }
}
