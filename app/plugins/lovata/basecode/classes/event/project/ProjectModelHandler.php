<?php namespace Lovata\BaseCode\Classes\Event\Project;

use Lovata\BaseCode\Classes\Store\ProjectListStore;
use Lovata\BaseCode\Models\Project;
use Lovata\Toolbox\Classes\Event\ModelHandler;
use Lovata\BaseCode\Classes\Item\ProjectItem;

/**
 * Class ProjectModelHandler
 * @package Lovata\BaseCode\Classes\Event\Project
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class ProjectModelHandler extends ModelHandler
{
    /** @var  Project */
    protected $obElement;

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();

        $this->checkFieldChanges('active', ProjectListStore::instance()->active);
        $this->checkFieldChanges('date', ProjectListStore::instance()->sorting);

        if ($this->isFieldChanged('category_id')) {
            ProjectListStore::instance()->category->clear($this->obElement->category_id);
            ProjectListStore::instance()->category->clear($this->obElement->getOriginal('category_id'));
        }
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        ProjectListStore::instance()->active->clear();
        ProjectListStore::instance()->sorting->clear();
        ProjectListStore::instance()->category->clear($this->obElement->category_id);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Project::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return ProjectItem::class;
    }
}
