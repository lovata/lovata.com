<?php namespace Lovata\MightySeo\Classes\Helper;

use Lang;
use System\Traits\ConfigMaker;
use October\Rain\Support\Traits\Singleton;

use Lovata\Toolbox\Traits\Helpers\TraitInitActiveLang;

/**
 * Class SeoConfigHelper
 * @package Lovata\MightySeo\Classes\Helper
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SeoConfigHelper
{
    use Singleton;
    use ConfigMaker;
    use TraitInitActiveLang;

    protected $arModelClassList = [];
    protected $arItemClassList = [];
    protected $arSeoValue = [];

    protected static $arActiveLangList = null;

    /**
     * Get model class list form config
     * @return array
     */
    public static function getModelClassList()
    {
        return self::instance()->arModelClassList;
    }
    /**
     * Get item class list form config
     * @return array
     */
    public static function getItemClassList()
    {
        return self::instance()->arItemClassList;
    }

    /**
     * Add seo param fields (with or without lang fields)
     * @param \Backend\Widgets\Form $obWidget
     * @throws \SystemException
     */
    public static function addSeoParamField($obWidget)
    {
        $obThis = self::instance();

        $arLangList = $obThis->getActiveLangList();
        if (empty($arLangList)) {
            $obThis->addSeoParamFieldDefault($obWidget);
            return;
        }

        $obThis->addSeoParamFieldTranslate($obWidget);
    }

    /**
     * Get relation field list (with or without lang fields)
     * @return array
     * @throws \SystemException
     */
    public static function getRelationFieldList()
    {
        $obThis = self::instance();

        $arLangList = $obThis->getActiveLangList();
        if (empty($arLangList)) {
            return $obThis->getRelationFieldListDefault();
        }

        return $obThis->getRelationFieldListTranslate();
    }

    /**
     * Get addition field list for SeoTemplate model form
     * @return array
     * @throws \SystemException
     */
    public static function getSeoTemplateFieldList()
    {
        $obThis = self::instance();

        $arLangList = $obThis->getActiveLangList();
        if (empty($arLangList)) {
            return [];
        }

        $arResult = [];
        $arFieldData = [
            'label'    => Lang::get('lovata.toolbox::lang.field.value'),
            'span'     => 'full',
            'required' => 1,
            'type'     => 'codeeditor',
            'size'     => 'large',
            'language' => 'twig'
        ];

        foreach ($arLangList as $sLangCode) {
            $arTempFieldData = $arFieldData;
            $arTempFieldData['label'] = $arTempFieldData['label'].' ('.$sLangCode.')';

            $arResult["lang[{$sLangCode}]"] = $arTempFieldData;
        }

        return $arResult;
    }

    /**
     * Get relation field values (with or without lang fields)
     * @param \Lovata\MightySeo\Models\SeoParam $obSeoParam
     * @return array
     * @throws \SystemException
     */
    public static function getRelationFieldValueList($obSeoParam)
    {
        if (empty($obSeoParam)) {
            return [];
        }

        $obThis = self::instance();
        if (isset($obThis->arSeoValue[$obSeoParam->id])) {
            return $obThis->arSeoValue[$obSeoParam->id];
        }

        $arResult = [
            'seo_title'             => $obSeoParam->seo_title,
            'seo_description'       => $obSeoParam->seo_description,
            'seo_keywords'          => $obSeoParam->seo_keywords,
            'page_h1'               => $obSeoParam->page_h1,
            'page_description'      => $obSeoParam->page_description,
            'robots_follow'         => $obSeoParam->robots_follow,
            'robots_index'          => $obSeoParam->robots_index,
            'robots_index_addition' => $obSeoParam->robots_index_addition,
            'canonical_url'         => $obSeoParam->canonical_url,
            'lang'                  => $obSeoParam->lang,
        ];

        $obThis->arSeoValue[$obSeoParam->id] = $arResult;
        return $arResult;
    }

    /**
     * Get relation lang field values
     * @param \Lovata\MightySeo\Models\SeoParam $obSeoParam
     * @return array
     */
    public static function getRelationLangFieldValueList($obSeoParam)
    {
        if (empty($obSeoParam)) {
            return [];
        }

        $obThis = self::instance();
        $arResult = [];

        $arLangList = $obThis->getActiveLangList();
        if (empty($arLangList)) {
            $obThis->arSeoValue['lang'.$obSeoParam->id] = $arResult;
            return $arResult;
        }

        foreach ($arLangList as $sLangCode) {
            $arResult[$sLangCode] = [
                'seo_title'        => $obSeoParam->getAttributeTranslated('seo_title', $sLangCode),
                'seo_description'  => $obSeoParam->getAttributeTranslated('seo_description', $sLangCode),
                'seo_keywords'     => $obSeoParam->getAttributeTranslated('seo_keywords', $sLangCode),
                'page_h1'          => $obSeoParam->getAttributeTranslated('page_h1', $sLangCode),
                'page_description' => $obSeoParam->getAttributeTranslated('page_description', $sLangCode),
                'canonical_url'    => $obSeoParam->getAttributeTranslated('canonical_url', $sLangCode),
            ];
        }

        $obThis->arSeoValue['lang'.$obSeoParam->id] = $arResult;

        return $arResult;
    }

    /**
     * Get relation lang field values
     * @param \Lovata\MightySeo\Models\SeoTemplate $obSeoTemplate
     * @return array
     */
    public static function getSeoTemplateLangFieldValueList($obSeoTemplate)
    {
        if (empty($obSeoTemplate)) {
            return [];
        }

        $obThis = self::instance();
        $arResult = [];

        $arLangList = $obThis->getActiveLangList();
        if (empty($arLangList)) {
            $obThis->arSeoValue['lang'.$obSeoTemplate->id] = $arResult;
            return $arResult;
        }

        foreach ($arLangList as $sLangCode) {
            $arResult[$sLangCode] = $obSeoTemplate->getAttributeTranslated('value', $sLangCode);
        }

        $obThis->arSeoValue['lang'.$obSeoTemplate->id] = $arResult;
        return $arResult;
    }

    /**
     * Init config data
     */
    protected function init()
    {
        $this->initModelClassList();
        $this->initItemClassList();

        $this->initActiveLang();
    }

    /**
     * Init model class list from config
     */
    protected function initModelClassList()
    {
        //Get model list form config
        /** @var array $arConfigList */
        $arConfigList = config('app.seo_models');
        if (empty($arConfigList)) {
            return;
        }

        //Process config list
        foreach ($arConfigList as $sClassName) {
            if (empty($sClassName) || !class_exists($sClassName)) {
                continue;
            }

            $this->arModelClassList[] = $sClassName;
        }
    }

    /**
     * Init item class list form config
     */
    protected function initItemClassList()
    {
        //Get model list form config
        /** @var array $arConfigList */
        $arConfigList = config('app.seo_items');
        if (empty($arConfigList)) {
            return;
        }

        //Process config list
        foreach ($arConfigList as $sClassName) {
            if (empty($sClassName) || !class_exists($sClassName)) {
                continue;
            }

            $this->arItemClassList[] = $sClassName;
        }
    }

    /**
     * Get relation field list (without translate)
     * @return array
     * @throws \SystemException
     */
    protected function getRelationFieldListDefault()
    {
        //Get field list from config file
        $obFieldList = $this->makeConfig('$/lovata/mightyseo/models/seoparam/default_relation_fields.yaml');
        if (empty($obFieldList)) {
            return [];
        }

        $arResult = [];
        foreach ($obFieldList->fields as $sKey => $arFieldData) {
            $arResult["seo_value[{$sKey}]"] = $arFieldData;
        }

        return $arResult;
    }

    /**
     * Get relation field list (with translate)
     * @return array
     * @throws \SystemException
     */
    protected function getRelationFieldListTranslate()
    {
        $arResult = [];

        $arLangList = $this->getActiveLangList();

        //Get field list from config file
        $obFieldList = $this->makeConfig('$/lovata/mightyseo/models/seoparam/translate_relation_fields.yaml');
        if (empty($obFieldList)) {
            return $arResult;
        }

        foreach ($obFieldList->fields as $sKey => $arFieldData) {
            if (!in_array($sKey, ['seo_title', 'page_h1', 'seo_description', 'seo_keywords', 'page_description', 'canonical_url'])) {
                $arResult["seo_value[{$sKey}]"] = $arFieldData;
                continue;
            }

            $arFieldData['label'] = Lang::get($arFieldData['label']);

            $arTempFieldData = $arFieldData;
            $arTempFieldData['label'] = $arTempFieldData['label'].' ('.self::$sDefaultLang.')';
            $arResult["seo_value[{$sKey}]"] = $arTempFieldData;

            $bSpanLeft = false;
            foreach ($arLangList as $sLangCode) {

                $arTempFieldData = $arFieldData;
                $arTempFieldData['label'] = $arTempFieldData['label'].' ('.$sLangCode.')';
                $arTempFieldData['span'] = $bSpanLeft ? 'left' : 'right';

                $arResult["seo_value[lang][{$sLangCode}][{$sKey}]"] = $arTempFieldData;
                $bSpanLeft = !$bSpanLeft;
            }
        }

        return $arResult;
    }

    /**
     * Add seo param fields (without translate)
     * @param \Backend\Widgets\Form $obWidget
     * @throws \SystemException
     */
    protected function addSeoParamFieldDefault($obWidget)
    {
        //Get field list from config file
        $obFieldList = $this->makeConfig('$/lovata/mightyseo/models/seoparam/default_fields.yaml');
        if (empty($obFieldList)) {
            return;
        }

        $obWidget->addFields($obFieldList->fields);
    }

    /**
     * Add seo param fields (with translate)
     * @param \Backend\Widgets\Form $obWidget
     * @throws \SystemException
     */
    protected function addSeoParamFieldTranslate($obWidget)
    {
        $arResult = [];

        $arLangList = $this->getActiveLangList();

        //Get field list from config file
        $obFieldList = $this->makeConfig('$/lovata/mightyseo/models/seoparam/translate_relation_fields.yaml');
        if (empty($obFieldList)) {
            return;
        }

        foreach ($obFieldList->fields as $sKey => $arFieldData) {
            if (!in_array($sKey, ['seo_title', 'page_h1', 'seo_description', 'seo_keywords', 'page_description', 'canonical_url'])) {
                $arResult[$sKey] = $arFieldData;
                continue;
            }

            $arFieldData['label'] = Lang::get($arFieldData['label']);

            $arTempFieldData = $arFieldData;
            $arTempFieldData['label'] = $arTempFieldData['label'].' ('.self::$sDefaultLang.')';
            $arResult[$sKey] = $arTempFieldData;

            $bSpanLeft = false;
            foreach ($arLangList as $sLangCode) {

                $arTempFieldData = $arFieldData;
                $arTempFieldData['label'] = $arTempFieldData['label'].' ('.$sLangCode.')';
                $arTempFieldData['span'] = $bSpanLeft ? 'left' : 'right';

                $arResult["lang[{$sLangCode}][{$sKey}]"] = $arTempFieldData;
                $bSpanLeft = !$bSpanLeft;
            }
        }

        $obWidget->addTabFields($arResult);
    }
}
