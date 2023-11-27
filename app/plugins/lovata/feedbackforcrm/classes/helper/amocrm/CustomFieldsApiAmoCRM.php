<?php namespace Lovata\FeedbackForCrm\Classes\Helper\AmoCrm;

/**
 * Class CustomFieldsApiAmoCRM
 *
 * @package Lovata\FeedbackForCrm\Classes\Helper\AmoCrm
 * @author Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class CustomFieldsApiAmoCRM extends ApiAmoCRM
{
    const URI_LEADS_CUSTOM_FIELDS = 'api/v4/leads/custom_fields';

    /**
     * Get list.
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList() : array
    {
        $sUrl = $this->sUrl.self::URI_LEADS_CUSTOM_FIELDS;

        $arResponse = $this->request(
            $sUrl,
            self::METHOD_GET,
            __METHOD__
        );

        return $arResponse;
    }
}
