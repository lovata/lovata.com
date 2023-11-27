<?php namespace Lovata\FeedbackForCrm\Classes\Helper\AmoCrm;

/**
 * Class PipelineApiAmoCRM
 *
 * @package Lovata\FeedbackForCrm\Classes\Helper\AmoCrm
 * @author Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class PipelineApiAmoCRM extends ApiAmoCRM
{
    const URI_LEADS_PIPELINES = 'api/v4/leads/pipelines';

    /**
     * Get list.
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList() : array
    {
        $sUrl = $this->sUrl.self::URI_LEADS_PIPELINES;

        $arResponse = $this->request(
            $sUrl,
            self::METHOD_GET,
            __METHOD__
        );

        return $arResponse;
    }
}
