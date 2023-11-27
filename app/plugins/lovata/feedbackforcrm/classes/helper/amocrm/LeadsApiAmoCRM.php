<?php namespace Lovata\FeedbackForCrm\Classes\Helper\AmoCrm;

/**
 * Class LeadsApiAmoCRM
 *
 * @package Lovata\FeedbackForCrm\Classes\Helper\AmoCrm
 * @author Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class LeadsApiAmoCRM extends ApiAmoCRM
{
    const URI_LEADS = 'api/v4/leads';

    /**
     * Add.
     * @param array $arRequest
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add($arRequest) : array
    {
        $sUrl = $this->sUrl.self::URI_LEADS;

        $arResponse = $this->request(
            $sUrl,
            self::METHOD_POST,
            __METHOD__,
            $arRequest
        );

        return $arResponse;
    }
}
