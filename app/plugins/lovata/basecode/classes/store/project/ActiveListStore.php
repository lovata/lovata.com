<?php namespace Lovata\BaseCode\Classes\Store\Project;

use Lovata\BaseCode\Models\Project;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

/**
 * Class ActiveListStore
 * @package Lovata\BaseCode\Classes\Store\Project
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ActiveListStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) Project::active()->lists('id');

        return $arElementIDList;
    }
}
