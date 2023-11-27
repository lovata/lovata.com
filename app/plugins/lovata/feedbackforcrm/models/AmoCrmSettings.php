<?php namespace Lovata\FeedbackForCrm\Models;

use Event;
use Lovata\FeedbackForCrm\Classes\Helper\AmoCrm\CustomFieldsApiAmoCRM;
use Lovata\FeedbackForCrm\Classes\Helper\AmoCrm\PipelineApiAmoCRM;
use Lovata\FeedbackForCrm\Classes\Helper\AmoCrm\UsersApiAmoCRM;
use October\Rain\Database\Model;

/**
 * Class AmoCrmSettings
 * @package Lovata\FeedbackForCrm\Models
 * @author Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class AmoCrmSettings extends Model
{
    const EVENT_FORM_LIST = 'feedbackforcrm.form_list';

    /** @var array */
    public $implement = ['System.Behaviors.SettingsModel'];
    /** @var string */
    public $settingsCode = 'lovata_feedback_for_crm_amo_crm_settings';
    /** @var string */
    public $settingsFields = 'fields.yaml';

    /**
     * Get form list.
     * @return array
     */
    public function getFormListOptions() : array
    {
        $arResult = [];
        $arEventResult = Event::fire(self::EVENT_FORM_LIST);

        if (empty($arEventResult)) {
            return $arResult;
        }

        foreach ($arEventResult as $arItem) {
            if (empty($arItem) || !is_array($arItem)) {
                continue;
            }

            $arResult = array_merge($arResult, $arItem);
        }

        return $arResult;
    }

    /**
     * Get pipeline list.
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPipelineListOptions() : array
    {
        $obPipelineApiAmoCRM = new PipelineApiAmoCRM();
        $arResponse = $obPipelineApiAmoCRM->getList();

        $arList   = array_get($arResponse, '_embedded.pipelines');
        $arResult = [];

        if (empty($arResponse)) {
            return $arResult;
        }

        foreach ($arList as $arItem) {
            $iId   = array_get($arItem, 'id');
            $sName = array_get($arItem, 'name');
            $arResult[$iId] = $sName;
        }

        return $arResult;
    }

    /**
     * Get responsible user list.
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getResponsibleUserListOptions() : array
    {
        $obPipelineApiAmoCRM = new UsersApiAmoCRM();
        $arResponse = $obPipelineApiAmoCRM->getList();

        $arList   = array_get($arResponse, '_embedded.users');
        $arResult = [];

        if (empty($arResponse)) {
            return $arResult;
        }

        foreach ($arList as $arItem) {
            $iId   = array_get($arItem, 'id');
            $sName = array_get($arItem, 'name');
            $arResult[$iId] = $sName;
        }

        return $arResult;
    }

    /**
     * Get custom field list.
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCustomFieldListOptions() : array
    {
        $obPipelineApiAmoCRM = new CustomFieldsApiAmoCRM();
        $arResponse = $obPipelineApiAmoCRM->getList();

        $arList   = array_get($arResponse, '_embedded.custom_fields');
        $arResult = [];

        if (empty($arResponse)) {
            return $arResult;
        }

        foreach ($arList as $arItem) {
            $iId   = array_get($arItem, 'id');
            $sName = array_get($arItem, 'name');
            $arResult[$iId] = $sName;
        }

        return $arResult;
    }
}
