<?php namespace Lovata\MightySeo\Models;

use Model;
use Kharanenka\Scope\ExternalIDField;

use Lovata\Toolbox\Classes\Helper\PageHelper;
use Lovata\Toolbox\Traits\Helpers\TraitCached;

use Lovata\MightySeo\Classes\Helper\SeoConfigHelper;

/**
 * Class SeoParam
 * @package Lovata\MightySeo\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property string                    $id
 * @property int                       $external_id
 * @property string                    $external_type
 * @property string                    $page_id
 * @property string                    $seo_title
 * @property string                    $seo_description
 * @property string                    $seo_keywords
 * @property string                    $page_h1
 * @property string                    $page_description
 * @property string                    $robots_follow
 * @property string                    $robots_index
 * @property string                    $robots_index_addition
 * @property string                    $canonical_url
 * @property array                     $lang
 * @property \October\Rain\Argon\Argon $updated_at
 * @property \October\Rain\Argon\Argon $created_at
 *
 * @property \Model                    $external
 * @method static \Model|\October\Rain\Database\Relations\MorphMany external()
 *
 * @method static $this getByPage(string $sKey)
 * @method static $this getByExternalType(string $sKey)
 */
class SeoParam extends Model
{
    use ExternalIDField;
    use TraitCached;

    public $table = 'lovata_mighty_seo_params';

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = [
        'seo_title',
        'seo_description',
        'seo_keywords',
        'page_h1',
        'page_description',
        'canonical_url',
    ];

    public $morphTo = [
        'external' => [],
    ];

    public $fillable = [
        'external_id',
        'external_type',
        'page_id',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'page_h1',
        'page_description',
        'robots_follow',
        'robots_index',
        'robots_index_addition',
        'canonical_url',
        'lang',
    ];

    public $cached = [
        'id',
        'page_id',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'page_h1',
        'page_description',
        'robots_follow',
        'robots_index',
        'robots_index_addition',
        'canonical_url',
    ];

    /**
     * Get element by page_id
     * @param SeoParam $obQuery
     * @param string   $sData
     * @return $this
     */
    public function scopeGetByPage($obQuery, $sData)
    {

        if (!empty($sData)) {
            $obQuery->where('page_id', $sData);
        }
        return $obQuery;
    }

    /**
     * Get element by external_type field
     * @param SeoParam $obQuery
     * @param string   $sData
     * @return $this
     */
    public function scopeGetByExternalType($obQuery, $sData)
    {

        if (!empty($sData)) {
            $obQuery->where('external_type', $sData);
        }
        return $obQuery;
    }

    /**
     * Get free page ID list
     * @return array
     */
    public function getPageIdOptions()
    {
        $arResult = PageHelper::instance()->getPageNameList();
        if (empty($arResult)) {
            return $arResult;
        }

        //Remove pages with SEO data
        $arPageList = self::whereNotNull('page_id')->pluck('page_id')->all();
        if (empty($arPageList)) {
            return $arResult;
        }

        foreach ($arPageList as $sPageCode) {

            if (!isset($arResult[$sPageCode]) || $sPageCode == $this->page_id) {
                continue;
            }

            unset($arResult[$sPageCode]);
        }

        return $arResult;
    }

    /**
     * Set lang attributes
     * @param array $arValueList
     */
    protected function setLangAttribute($arValueList)
    {
        if (empty($arValueList) || !is_array($arValueList)) {
            return;
        }

        foreach ($arValueList as $sLangKey => $arFieldList) {
            if (empty($arFieldList) || !is_array($arFieldList)) {
                continue;
            }

            foreach ($arFieldList as $sFieldName => $sValue) {
                $this->setAttributeTranslated($sFieldName, $sValue, $sLangKey);
            }
        }
    }

    /**
     * Get lang attributes
     * @array array
     */
    protected function getLangAttribute()
    {
        $arResult = SeoConfigHelper::getRelationLangFieldValueList($this);

        return $arResult;
    }

    /**
     * Get page name attribute
     * @return string
     */
    protected function getPageNameAttribute()
    {
        if (empty($this->page_id)) {
            return null;
        }

        $arPageList = PageHelper::instance()->getPageNameList();
        if (empty($arPageList) || !isset($arPageList[$this->page_id])) {
            return $this->page_id;
        }

        return $arPageList[$this->page_id];
    }
}
