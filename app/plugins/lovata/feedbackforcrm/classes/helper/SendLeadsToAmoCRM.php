<?php namespace Lovata\FeedbackForCrm\Classes\Helper;

use Event;
use October\Rain\Argon\Argon;
use Lovata\FeedbackForCrm\Models\AmoCrmSettings;
use Lovata\FeedbackForCrm\Classes\Helper\AmoCrm\LeadsApiAmoCRM;

/**
 * Class SendLeadsToAmoCRM
 *
 * @package Lovata\FeedbackForCrm\Classes\Helper
 * @author Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class SendLeadsToAmoCRM
{
    const EVENT_EXTEND_REQUEST_DATA_BEFORE_SEND_LEADS = 'feedback_for_crm.extend_request_data_before_send_leads';

    /** @var array */
    protected $arSettings = [];
    /** @var array */
    protected $arFormSettings = [];

    /**
     * SendLeadsToAmoCRM constructor.
     */
    public function __construct()
    {
        $this->arSettings = AmoCrmSettings::get('feedback_form', []);
    }

    /**
     * Send.
     * @param array  $arData
     * @param string $sForm
     * @param string $sPipelineValue
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($arData, $sForm, $sPipelineValue = '')
    {
        if (empty($arData) || !is_array($arData) || empty($sForm)) {
            return;
        }

        foreach ($this->arSettings as $arItemSettings) {
            $sPipelineValueSettings = array_get($arItemSettings, 'pipeline_value', '');
            $sFormSettings          = array_get($arItemSettings, 'form', '');

            if (empty($sFormSettings)) {
                continue;
            }

            if ($sForm == $sFormSettings && $sPipelineValue == $sPipelineValueSettings) {
                $this->arFormSettings = $arItemSettings;

                break;
            }
        }

        if (empty($this->arFormSettings)) {
            return;
        }

        $arRequestData = $this->getRequestData($arData, $sForm, $sPipelineValue);

        $obLeadsApiAmoCRM = new LeadsApiAmoCRM();
        $obLeadsApiAmoCRM->add($arRequestData);
    }

    /**
     * Get request data.
     * @param array  $arData
     * @param string $sForm
     * @param string $sPipelineValue
     * @return array
     */
    protected function getRequestData($arData, $sForm, $sPipelineValue = '') : array
    {
        $arRequestData = [
            'created_at' => Argon::now()->timestamp,
            'updated_at' => Argon::now()->timestamp,
        ];

        // Pipeline.
        $sPipelineId = (int) array_get($this->arFormSettings, 'pipeline_id');
        if (!empty($sPipelineId)) {
            array_set($arRequestData, 'pipeline_id', $sPipelineId);
        }
        // Responsible user.
        $iResponsibleUserId = (int) array_get($this->arFormSettings, 'responsible_user_id');
        if (!empty($iResponsibleUserId)) {
            array_set($arRequestData, 'responsible_user_id', $iResponsibleUserId);
        }
        // Tags.
        $arTags = $this->getRequestTags(array_get($this->arFormSettings, 'tags'));
        if (!empty($arTags)) {
            array_set($arRequestData, '_embedded.tags', $arTags);
        }
        // Custom fields.
        $arCustomFields = $this->getRequestCustomFields($arData);
        if (!empty($arCustomFields)) {
            array_set($arRequestData, 'custom_fields_values', $arCustomFields);
        }

        $arRequestData = $this->extendRequestData($arRequestData, $arData, $sForm, $sPipelineValue);
        $arRequestData = [$arRequestData];

        return $arRequestData;
    }

    /**
     * Get request tags.
     * @param string $sTags
     * @return array
     */
    protected function getRequestTags($sTags) : array
    {
        $arResult = [];

        if (empty($sTags) || !is_string($sTags)) {
            return $arResult;
        }

        $arTags = explode(',', $sTags);

        foreach ($arTags as $sTag) {
            $sTag = trim($sTag);

            if (!empty($sTag)) {
                $arResult[] = ['name' => $sTag];
            }

        }

        return $arResult;
    }

    /**
     * Get request custom fields.
     * @param array $arData
     * @return array
     */
    protected function getRequestCustomFields($arData) : array
    {
        $arResult = [];

        $arCustomFieldsSettings = array_get($this->arFormSettings, 'custom_fields', []);

        if (empty($arCustomFieldsSettings)) {
            return [];
        }

        foreach ($arCustomFieldsSettings as $arFieldSettings) {
            $sKey     = array_get($arFieldSettings, 'key');
            $iFieldId = (int) array_get($arFieldSettings, 'field_id');
            $sValue   = array_get($arData, $sKey);

            if (empty($sKey) || empty($iFieldId) || empty($sValue)) {
                continue;
            }

            $arResult[] = [
                'field_id' => $iFieldId,
                'values' => [
                    [
                        'value' => $sValue,
                    ],
                ],
            ];
        }

        return $arResult;
    }

    /**
     * Extend request data.
     * @param array  $arRequestData
     * @param array  $arData
     * @param string $sForm
     * @param string $sPipelineValue
     * @return array
     */
    protected function extendRequestData($arRequestData, $arData, $sForm, $sPipelineValue = '') : array
    {
        $arEventData = [$arRequestData, $arData, $sForm, $sPipelineValue];
        $arEventResult = Event::fire(self::EVENT_EXTEND_REQUEST_DATA_BEFORE_SEND_LEADS, $arEventData);

        if (empty($arEventResult)) {
            return $arRequestData;
        }

        foreach ($arEventResult as $arItem) {
            if (empty($arItem) || !is_array($arItem)) {
                continue;
            }

            $arRequestData = array_merge($arRequestData, $arItem);
        }

        return $arRequestData;
    }
}
