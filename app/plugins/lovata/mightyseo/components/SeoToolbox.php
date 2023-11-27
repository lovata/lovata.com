<?php namespace Lovata\MightySeo\Components;

use App;
use Cms\Classes\ComponentBase;

use Lovata\MightySeo\Models\SeoParam;
use Lovata\MightySeo\Models\Settings;
use Lovata\MightySeo\Classes\Item\SeoParamItem;
use Lovata\MightySeo\Classes\TemplateProcessor;

/**
 * Class SeoToolbox
 * @package Lovata\MightySeo\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SeoToolbox extends ComponentBase
{
    protected $arTemplateParams = [];
    protected $sPageID;

    /** @var SeoParam|SeoParamItem */
    protected $obModelSeoParam;

    /** @var SeoParamItem */
    protected $obPageSeoParamItem;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.mightyseo::lang.component.seo_toolbox_name',
            'description' => 'lovata.mightyseo::lang.component.seo_toolbox_description',
        ];
    }

    /**
     * onRun component method
     */
    public function onRun()
    {
        //Get page ID
        $this->sPageID = $this->page->id;
        if (empty($this->sPageID) && !empty($this->page['page'])) {

            $obPage = $this->page['page'];
            $this->sPageID = $obPage->id;
        }

        //Get SeoParam item for page
        $this->obPageSeoParamItem = SeoParamItem::make($this->sPageID);
    }

    /**
     * obRender component method
     */
    public function onRender()
    {
        /** @var \Model|\Lovata\Toolbox\Classes\Item\ElementItem $obModelObject */
        $obModelObject = $this->property('model');

        /** @var array $arPropertyList */
        $arPropertyList = $this->property('params');

        $this->initData($obModelObject, $arPropertyList);
    }

    /**
     * Get SEO page title
     * @return string
     */
    public function getTitle()
    {
        $sResult = Settings::getValue('seo_title_prefix').' '.$this->get('seo_title').' '.Settings::getValue('seo_title_suffix');
        $sResult = trim($sResult);

        return $sResult;
    }

    /**
     * Get settings value
     * @return null|string
     */
    public function getHeadBegin()
    {
        return Settings::getValue('head_begin');
    }

    /**
     * Get settings value
     * @return null|string
     */
    public function getHeadEnd()
    {
        return Settings::getValue('head_end');
    }

    /**
     * Get settings value
     * @return null|string
     */
    public function getBodyBegin()
    {
        return Settings::getValue('body_begin');
    }

    /**
     * Get settings value
     * @return null|string
     */
    public function getBodyEnd()
    {
        return Settings::getValue('body_end');
    }

    /**
     * Get content for meta tag robots
     * @return string
     */
    public function getRobotsMeta()
    {
        $arMetaTagList = [];
        $arMetaTagList[] = $this->get('robots_index');
        $arMetaTagList[] = $this->get('robots_follow');

        $arAdditionTagList = explode(',', $this->get('robots_index_addition'));
        if (!empty($arAdditionTagList)) {
            $arMetaTagList = array_merge($arMetaTagList, $arAdditionTagList);
        }

        $sResult = '';
        foreach ($arMetaTagList as $sTag) {

            $sTag = trim($sTag);
            if (empty($sTag)) {
                continue;
            }

            $sResult .= $sTag.',';
        }

        $sResult = rtrim($sResult, ',');

        return $sResult;
    }

    /**
     * Get H1 page title
     * @param SeoParam|SeoParamItem|null $obModel
     * @param array|null                 $arTemplateParams
     * @return string
     */
    public function getPageTitle($obModel = null, $arTemplateParams = null)
    {
        $this->initData($obModel, $arTemplateParams);
        $sResult = $this->get('page_h1');

        return $sResult;
    }

    /**
     * Get page description
     * @param SeoParam|SeoParamItem|null $obModel
     * @param array|null                 $arTemplateParams
     * @return string
     */
    public function getPageDescription($obModel = null, $arTemplateParams = null)
    {
        $this->initData($obModel, $arTemplateParams);
        $sResult = $this->get('page_description');

        return $sResult;
    }

    /**
     * Get seo string
     * @param string $sKey
     * @return string
     */
    public function get($sKey)
    {
        if (empty($sKey)) {
            return null;
        }

        //Get SEO field from model
        $sResult = $this->getModelField($sKey);
        if (!empty($sResult)) {
            return $sResult;
        }

        //Get SEO field from page
        $sResult = $this->getPageField($sKey);

        return $sResult;
    }

    /**
     * Get SEO field from model
     * @param string $sKey
     * @return string
     */
    protected function getModelField($sKey)
    {
        if (empty($sKey) || empty($this->obModelSeoParam)) {
            return null;
        }

        //Get field value
        $sFieldValue = $this->obModelSeoParam->$sKey;
        if (!$this->isNeedProcess($sKey)) {
            $sFieldValue = trim($sFieldValue);
            return $sFieldValue;
        }

        //Process string with TemplateProcessor
        /** @var TemplateProcessor $obTemplateProcessor */
        $obTemplateProcessor = App::make(TemplateProcessor::class, [
            'sValue' => $sFieldValue, 
            'arParamList' => $this->arTemplateParams, 
            'bOneLine' => $this->isOneLineParam($sKey)
        ]);
        $sResult = $obTemplateProcessor->run();

        return $sResult;
    }

    /**
     * Get SEO field from page
     * @param string $sKey
     * @return string
     */
    protected function getPageField($sKey)
    {
        if (empty($sKey) || empty($this->obPageSeoParamItem)) {
            return null;
        }

        //Get field value
        $sFieldValue = $this->obPageSeoParamItem->$sKey;
        if (!$this->isNeedProcess($sKey)) {
            $sFieldValue = trim($sFieldValue);
            return $sFieldValue;
        }

        //Process string with TemplateProcessor
        /** @var TemplateProcessor $obTemplateProcessor */
        $obTemplateProcessor = App::make(TemplateProcessor::class, [
            'sValue' => $sFieldValue, 
            'arParamList' => $this->arTemplateParams, 
            'bOneLine' => $this->isOneLineParam($sKey)
        ]);
        $sResult = $obTemplateProcessor->run();

        return $sResult;
    }

    /**
     * Init SEO objects
     * @param \Model|\Lovata\Toolbox\Classes\Item\ElementItem $obModel
     * @param array                                           $arTemplateParams
     */
    protected function initData($obModel, $arTemplateParams)
    {
        if (is_array($arTemplateParams)) {
            $this->arTemplateParams = $arTemplateParams;
        }

        if (empty($obModel)) {
            $this->obModelSeoParam = null;
            return;
        }

        $this->obModelSeoParam = $obModel->seo_param;
        if (!$this->obModelSeoParam instanceof SeoParamItem) {
            $this->obModelSeoParam = null;
        }
    }

    /**
     * Check, string need to convert in one line
     * @param string $sKey
     * @return bool
     */
    protected function isOneLineParam($sKey)
    {
        return !in_array($sKey, ['page_h1', 'page_description']);
    }

    /**
     * Check, string need to process with TemplateProcessor
     * @param string $sKey
     * @return bool
     */
    protected function isNeedProcess($sKey)
    {
        return !in_array($sKey, ['robots_follow', 'robots_index', 'robots_index_addition', 'canonical_url']);
    }
}
