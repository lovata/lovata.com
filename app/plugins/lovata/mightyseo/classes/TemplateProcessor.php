<?php namespace Lovata\MightySeo\Classes;

use App;
use Twig;
use Lovata\MightySeo\Classes\Item\SeoTemplateItem;

/**
 * Class TemplateProcessor
 * @package Lovata\MightySeo\Classes
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class TemplateProcessor
{
    /** @var  string */
    protected $sValue;

    /** @var  string */
    protected $sResult;

    /** @var  array */
    protected $arParamList = [];

    /** @var bool */
    protected $bOneLine = false;

    /**
     * TemplateProcessor constructor.
     *
     * @param string $sValue
     * @param array  $arParamList
     * @param bool   $bOneLine
     */
    public function __construct($sValue = null, $arParamList = null, $bOneLine = false)
    {
        $this->bOneLine = $bOneLine;
        if (empty($sValue) || !is_string($sValue)) {
            return;
        }

        //Set key value
        $this->sValue = trim($sValue);

        if (empty($arParamList) || !is_array($arParamList)) {
            return;
        }

        $this->arParamList = $arParamList;
    }

    /**
     * @return string
     */
    public function run()
    {
        if (empty($this->sValue)) {
            return null;
        }

        $this->processSeoTemplate();

        try {
            $this->sResult = Twig::parse($this->sValue, $this->arParamList);
        } catch (\Exception $obException) {
            return null;
        }

        $this->convertOneLine();
        $this->sResult = trim($this->sResult);

        return $this->sResult;
    }

    /**
     * Replace calls of seo_template twig function with template values
     */
    protected function processSeoTemplate()
    {
        $arMatches = [];
        if (!preg_match_all('%{{\s*seo_template\s*\(\s*[\'\"](.+)[\'\"]\s*\)\s*}}%', $this->sValue, $arMatches, PREG_SET_ORDER)) {
            return;
        }

        //Process matches list
        foreach ($arMatches as $arMatchData) {
            $sTemplateCode = array_shift($arMatchData);
            $sTemplateKey = array_shift($arMatchData);

            //Get SEO template item
            $obSeoTemplate = SeoTemplateItem::make($sTemplateKey);

            /** @var TemplateProcessor $obTemplateProcessor */
            $obTemplateProcessor = App::make(self::class, [
                'sValue' => $obSeoTemplate->value,
                'arParamList' => $this->arParamList,
                'bOneLine' => $this->bOneLine]);
            $this->sResult = $obTemplateProcessor->run();

            $this->sValue = str_replace($sTemplateCode, $this->sResult, $this->sValue);
        }
    }

    /**
     * Convert result string in one line string
     */
    protected function convertOneLine()
    {
        if (!$this->bOneLine) {
            return;
        }

        $this->sResult = str_replace(["\n", "\r\n"], ' ', $this->sResult);
        $this->sResult = str_replace("\t", ' ', $this->sResult);
        $this->sResult = str_replace('  ', ' ', $this->sResult);
    }
}
