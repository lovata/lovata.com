<?php namespace Lovata\MightySeo\Models;

use Model;
use Kharanenka\Scope\ExternalIDField;
use October\Rain\Database\Traits\Validation;
use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\MightySeo\Classes\Helper\SeoConfigHelper;

/**
 * Class SeoTemplate
 * @package Lovata\MightySeo\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property string                    $id
 * @property string                    $key
 * @property string                    $value
 * @property int                       $external_id
 * @property string                    $external_type
 * @property array                     $lang
 * @property \October\Rain\Argon\Argon $updated_at
 * @property \October\Rain\Argon\Argon $created_at
 *
 * @method static $this getByKey(string $sKey)
 * @method static $this getByExternalType(string $sKey)
 */
class SeoTemplate extends Model
{
    use Validation;
    use ExternalIDField;
    use TraitCached;

    public $table = 'lovata_mighty_seo_templates';

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = [
        'value',
    ];

    public $rules = [
        'key' => 'required|unique:lovata_mighty_seo_templates|max:255',
    ];

    public $attributeNames = [
        'key' => 'lovata.toolbox::lang.field.key',
    ];

    public $morphTo = [
        'external' => [],
    ];

    public $fillable = [
        'key',
        'value',
        'external_id',
        'external_type',
        'lang',
    ];

    public $cached = [
        'id',
        'key',
        'value',
    ];

    /**
     * Get element by key
     * @param SeoTemplate $obQuery
     * @param string      $sData
     * @return $this
     */
    public function scopeGetByKey($obQuery, $sData)
    {

        if (!empty($sData)) {
            $obQuery->where('key', $sData);
        }
        return $obQuery;
    }

    /**
     * Get element by external_type field
     * @param SeoTemplate $obQuery
     * @param string      $sData
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
     * Set lang attributes
     * @param array $arValueList
     */
    protected function setLangAttribute($arValueList)
    {
        if (empty($arValueList) || !is_array($arValueList)) {
            return;
        }

        foreach ($arValueList as $sLangKey => $sValue) {
            $this->setAttributeTranslated('value', $sValue, $sLangKey);
        }
    }

    /**
     * Get lang attributes
     * @array array
     */
    protected function getLangAttribute()
    {
        $arResult = SeoConfigHelper::getSeoTemplateLangFieldValueList($this);

        return $arResult;
    }
}
