<?php namespace Lovata\FeedbackForCrm\Classes\Helper\AmoCrm;

/**
 * Class UsersApiAmoCRM
 *
 * @package Lovata\FeedbackForCrm\Classes\Helper\AmoCrm
 * @author Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class UsersApiAmoCRM extends ApiAmoCRM
{
    const URI_USERS = 'api/v4/users';

    /**
     * Get list.
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList() : array
    {
        $sUrl = $this->sUrl.self::URI_USERS;

        $arResponse = $this->request(
            $sUrl,
            self::METHOD_GET,
            __METHOD__
        );

        return $arResponse;
    }
}
