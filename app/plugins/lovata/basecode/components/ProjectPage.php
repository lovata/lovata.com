<?php namespace Lovata\BaseCode\Components;

use Lovata\BaseCode\Classes\Item\ProjectItem;
use Lovata\BaseCode\Models\Project;
use Lovata\Toolbox\Classes\Component\ElementPage;

/**
 * Class ProjectPage
 * @package Lovata\BaseCode\Components
 * @author Sergey Zakharevich, <s.v.zakharevich@gmail.com>, LOVATA Group
 */
class ProjectPage extends ElementPage
{
    /** @var Project */
    protected $obElement;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.basecode::lang.component.project_page',
            'description' => 'lovata.basecode::lang.component.project_page_desc'
        ];
    }

    /**
     * Get element object
     * @param string $sElementSlug
     * @return Project
     */
    protected function getElementObject($sElementSlug)
    {
        if (empty($sElementSlug)) {
            return null;
        }

        $obElement = Project::active()->getBySlug($sElementSlug)->first();

        return $obElement;
    }

    /**
     * Make new element item
     * @param int $iElementID
     * @param Project $obElement
     * @return ProjectItem
     */
    protected function makeItem($iElementID, $obElement)
    {
        return ProjectItem::make($iElementID, $obElement);
    }
}
