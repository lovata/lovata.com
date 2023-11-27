<?php namespace Lovata\BaseCode\Classes\Item;

use Cms\Classes\Page as CmsPage;
use Lovata\BaseCode\Models\Project;
use Lovata\Toolbox\Classes\Item\ElementItem;

/**
 * Class ProjectItem
 *
 * @package Lovata\BaseCode\Classes\Item
 * @author  Sergey Zakharevich, <s.v.zakharevich@gmail.com>, LOVATA Group
 * @property int                       $id
 * @property boolean                   $active
 * @property string                    $title
 * @property string                    $preview_title
 * @property string                    $slug
 * @property integer                   $category_id
 * @property string                    $project_url
 * @property string                    $project_name
 * @property string                    $preview_text
 * @property string                    $preview_text_inner_page
 * @property array                     $content
 * @property array                     $tags
 *
 * @property \System\Models\File       $preview_image
 *
 * @property \October\Rain\Argon\Argon $date
 */
class ProjectItem extends ElementItem
{
    const MODEL_CLASS = Project::class;

    /**
     * Returns URL of a category page.
     * @param string $sPageCode
     * @return string
     */
    public function getPageUrl($sPageCode = 'project')
    {
        $sURL = CmsPage::url($sPageCode, ['slug' => $this->slug]);

        return $sURL;
    }
}
