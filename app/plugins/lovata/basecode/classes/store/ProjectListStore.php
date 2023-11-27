<?php namespace Lovata\BaseCode\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;
use Lovata\BaseCode\Classes\Store\Project\ActiveListStore;
use Lovata\BaseCode\Classes\Store\Project\SortingListStore;
use Lovata\BaseCode\Classes\Store\Project\ListByCategoryStore;

/**
 * Class ProjectListStore
 *
 * @package Lovata\BaseCode\Classes\Store
 * @author  Sergey Zakharevich, <s.v.zakharevich@gmail.com>, LOVATA Group
 * @property ActiveListStore     $active
 * @property SortingListStore    $sorting
 * @property ListByCategoryStore $category
 */
class ProjectListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('category', ListByCategoryStore::class);
        $this->addToStoreList('sorting', SortingListStore::class);
        $this->addToStoreList('active', ActiveListStore::class);
    }
}
