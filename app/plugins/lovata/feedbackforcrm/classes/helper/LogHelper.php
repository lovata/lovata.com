<?php namespace Lovata\FeedbackForCrm\Classes\Helper;

/**
 * Class LogHelper
 *
 * @package Lovata\FeedbackForCrm\Classes\Helper
 * @author Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class LogHelper
{
    /**
     * Error.
     * @param string $sClassMethod
     * @param string $sErrorMessage
     * @param array  $arRequest
     * @param array  $arResponse
     * @param array  $arHeaders
     */
    public static function error($sClassMethod = '', $sErrorMessage = '', $arRequest = [], $arResponse = [])
    {
        if (!is_array($arRequest)) {
            $arRequest = [];
        }

        if (!is_array($arResponse)) {
            $arResponse = [];
        }

        $sMessage  = '';
        $arMessage = [
            'class_method' => $sClassMethod,
            'message'      => $sErrorMessage,
            'request'      => json_encode($arRequest, true),
            'response'     => json_encode($arResponse, true),
        ];

        foreach ($arMessage as $sKey => $sValue) {
            if (!empty($sValue)) {
                $sMessage .= $sKey.': '.$sValue;
            }
        }

        \Log::error($sMessage);
    }
}
