<?php namespace Lovata\FeedbackForCrm\Classes\Queue;

use Exception;
use Illuminate\Queue\Jobs\Job;
use Lovata\FeedbackForCrm\Classes\Helper\SendLeadsToAmoCRM;

/**
 * Class SendLeadsToAmoCRMQueue
 *
 * @package Lovata\FeedbackForCrm\Classes\Queue
 * @author Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class SendLeadsToAmoCRMQueue
{
    const QUEUE_NAME = 'feedbackforcrm.amo_crm';

    /**
     * Fire method.
     * @param Job   $obJob
     * @param array $arData
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fire($obJob, $arData)
    {
        $this->send($arData);
        $obJob->delete();
    }

    /**
     * Send.
     * @param array $arData
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function send($arData)
    {
        if (!is_array($arData)) {
            return;
        }

        $sForm          = array_get($arData, 'form');
        $sPipelineValue = array_get($arData, 'pipeline_value', '');
        $arData         = array_get($arData, 'data');

        $obSendLeadsToAmoCRM = new SendLeadsToAmoCRM();
        $obSendLeadsToAmoCRM->send($arData, $sForm, $sPipelineValue);
    }
}
