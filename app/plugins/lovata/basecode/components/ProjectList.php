<?php namespace Lovata\BaseCode\Components;

use Cms\Classes\ComponentBase;
use Lovata\BaseCode\Classes\Collection\ProjectCollection;
use Lovata\BaseCode\Models\Project;

/**
 * Class ProjectList
 * @package Lovata\BaseCode\Components
 * @author Sergey Zakharevich, <s.v.zakharevich@gmail.com>, LOVATA Group
 */
class ProjectList extends ComponentBase
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'lovata.basecode::lang.component.project_list',
            'description'   => 'lovata.basecode::lang.component.project_list_desc',
        ];
    }

    /**
     * Make element collection
     * @param array $arElementIDList
     * @return ProjectCollection
     */
    public function make($arElementIDList = null)
    {
        return ProjectCollection::make($arElementIDList);
    }

    /**
     * Method for ajax request with empty response
     * @return bool
     */
    public function onAjaxRequest()
    {
        return true;
    }

    /**
     * Category healthcare projects.
     * @return int
     */
    public function categoryHealthcareProjects() : int
    {
        return Project::CATEGORY_HEALTHCARE_PROJECTS_ID;
    }

    /**
     * Category healthcare projects.
     * @return int
     */
    public function categoryRetailAndEcommerce() : int
    {
        return Project::CATEGORY_RETAIL_AND_ECOMMERCE_ID;
    }

    /**
     * Category sharing economy and services.
     * @return int
     */
    public function categorySharingEconomyAndServices() : int
    {
        return Project::CATEGORY_SHARING_ECONOMY_AND_SERVICES_ID;
    }

    /**
     * Category other projects.
     * @return int
     */
    public function categoryOtherProjects() : int
    {
        return Project::CATEGORY_OTHER_PROJECTS_ID;
    }
}
