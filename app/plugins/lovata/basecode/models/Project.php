<?php namespace Lovata\BaseCode\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Kharanenka\Scope\SlugField;
use Kharanenka\Scope\CategoryBelongsTo;
use Kharanenka\Scope\ActiveField;
use Lovata\Toolbox\Traits\Helpers\TraitCached;

/**
 * Class Project
 *
 * @package Lovata\BaseCode\Models
 * @author  Sergey Zakharevich, <s.v.zakharevich@gmail.com>, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
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
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 */
class Project extends Model
{
    use Validation;
    use SlugField;
    use TraitCached;
    use CategoryBelongsTo;
    use ActiveField;

    const CATEGORY_HEALTHCARE_PROJECTS_ID          = 1;
    const CATEGORY_RETAIL_AND_ECOMMERCE_ID         = 2;
    const CATEGORY_SHARING_ECONOMY_AND_SERVICES_ID = 3;
    const CATEGORY_OTHER_PROJECTS_ID               = 4;

    /** @var string */
    public $table = 'lovata_basecode_projects';
    /** @var array */
    public $rules = [
        'title'       => 'required',
        'category_id' => 'required',
        'date'        => 'required',
        'slug'        => 'required|unique:lovata_basecode_projects',
    ];
    /** @var array */
    public $attributeNames = [
        'title'       => 'lovata.toolbox::lang.field.title',
        'slug'        => 'lovata.toolbox::lang.field.slug',
        'date'        => 'lovata.basecode::lang.field.date',
        'category_id' => 'lovata.toolbox::lang.field.category',
    ];
    /** @var array */
    public $fillable = [
        'active',
        'title',
        'preview_text',
        'slug',
        'category_id',
        'project_url',
        'project_name',
        'date',
        'preview_image',
        'preview_title',
        'preview_text_inner_page',
        'content',
        'tags',
    ];
    /** @var array */
    public $jsonable = [
        'content',
        'tags',
    ];
    /** @var array */
    public $dates = [
        'date',
        'created_at',
        'updated_at',
    ];
    /** @var array */
    public $cached = [
        'id',
        'active',
        'title',
        'preview_text',
        'slug',
        'category_id',
        'project_url',
        'project_name',
        'date',
        'preview_image',
        'preview_title',
        'preview_text_inner_page',
        'content',
        'tags'
    ];
    /** @var array */
    public $attachOne = ['preview_image' => 'System\Models\File'];

    /**
     * Get category id list options.
     * @return array
     */
    public function getCategoryIdListOptions() : array
    {
        return [
            self::CATEGORY_HEALTHCARE_PROJECTS_ID          => trans('lovata.basecode::lang.field.category_'.self::CATEGORY_HEALTHCARE_PROJECTS_ID),
            self::CATEGORY_RETAIL_AND_ECOMMERCE_ID         => trans('lovata.basecode::lang.field.category_'.self::CATEGORY_RETAIL_AND_ECOMMERCE_ID),
            self::CATEGORY_SHARING_ECONOMY_AND_SERVICES_ID => trans('lovata.basecode::lang.field.category_'.self::CATEGORY_SHARING_ECONOMY_AND_SERVICES_ID),
            self::CATEGORY_OTHER_PROJECTS_ID               => trans('lovata.basecode::lang.field.category_'.self::CATEGORY_OTHER_PROJECTS_ID),
        ];
    }
}
