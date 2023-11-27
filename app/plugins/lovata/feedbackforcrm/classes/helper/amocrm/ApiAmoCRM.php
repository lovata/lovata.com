<?php namespace Lovata\FeedbackForCrm\Classes\Helper\AmoCrm;

use Lovata\FeedbackForCrm\Classes\Helper\LogHelper;
use Lovata\FeedbackForCrm\Models\AmoCrmSettings;
use GuzzleHttp\Client;
use October\Rain\Argon\Argon;

/**
 * Class ApiAmoCRM
 *
 * @package Lovata\FeedbackForCrm\Classes\Helper\AmoCrm
 * @author Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class ApiAmoCRM
{
    const METHOD_POST = 'POST';
    const METHOD_GET  = 'GET';

    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    const GRANT_TYPE_REFRESH_TOKEN      = 'refresh_token';

    const URL_AMO_CRM = 'https://{{domain}}.amocrm.ru/';

    const URI_OAUTH2_ACCESS_TOKEN = 'oauth2/access_token';

    /** @var string */
    protected $sClientId;
    /** @var string */
    protected $sClientSecret;
    /** @var string */
    protected $sCode;
    /** @var string */
    protected $sRedirectUri;
    /** @var string */
    protected $sDomainName;
    /** @var boolean */
    protected $bActive = true;
    /** @var string */
    protected $sUrl = '';
    /** @var string */
    protected $sToken;
    /** @var string */
    protected $sRefreshToken;
    /** @var string */
    protected $sTokenExpirationDate;

    /**
     * MainAmoCRM constructor.
     */
    public function __construct()
    {
        $this->sClientId     = AmoCrmSettings::get('client_id', '');
        $this->sClientSecret = AmoCrmSettings::get('client_secret', '');
        $this->sCode         = AmoCrmSettings::get('code', '');
        $this->sRedirectUri  = AmoCrmSettings::get('redirect_uri', '');
        $this->sDomainName   = AmoCrmSettings::get('domain_name', '');

        if (!$this->bActive
            || empty($this->sClientId)
            || empty($this->sClientSecret)
            || empty($this->sCode)
            || empty($this->sRedirectUri)
            || empty($this->sDomainName)
        ) {
            $this->bActive = false;

            return;
        }

        $this->sUrl                 = str_replace('{{domain}}', $this->sDomainName, self::URL_AMO_CRM);
        $this->sToken               = AmoCrmSettings::get('access_token', '');
        $this->sRefreshToken        = AmoCrmSettings::get('refresh_token', '');
        $this->sTokenExpirationDate = AmoCrmSettings::get('token_expiration_date');
    }

    /**
     * Get token.
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function token() : array
    {
        $sUrl = $this->sUrl.self::URI_OAUTH2_ACCESS_TOKEN;

        $arRequest  = [
            'client_id'     => $this->sClientId,
            'client_secret' => $this->sClientSecret,
            'grant_type'    => self::GRANT_TYPE_AUTHORIZATION_CODE,
            'code'          => $this->sCode,
            'redirect_uri'  => $this->sRedirectUri,
        ];

        $arResponse = $this->request(
            $sUrl,
            self::METHOD_POST,
            __METHOD__,
            $arRequest,
            false
        );

        $this->saveToken($arResponse);

        return $arResponse;
    }

    /**
     * Get refresh token.
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshToken() : array
    {
        if (empty($this->sRefreshToken)) {
            return [];
        }

        $sUrl = $this->sUrl.self::URI_OAUTH2_ACCESS_TOKEN;

        $arRequest  = [
            'client_id'     => $this->sClientId,
            'client_secret' => $this->sClientSecret,
            'grant_type'    => self::GRANT_TYPE_REFRESH_TOKEN,
            'refresh_token' => $this->sRefreshToken,
            'redirect_uri'  => $this->sRedirectUri,
        ];

        $arResponse = $this->request($sUrl,
            self::METHOD_POST,
            __METHOD__,
            $arRequest,
            false
        );

        $this->saveToken($arResponse);

        return $arResponse;
    }

    /**
     * Request
     * @param string  $sUrl
     * @param string  $sMethod
     * @param string  $sClassMethod
     * @param array   $arRequest
     * @param boolean $bAuth
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request($sUrl, $sMethod, $sClassMethod = '', $arRequest = [], $bAuth = true) : array
    {
        if (!$this->bActive || empty($sUrl) || !is_array($arRequest)) {
            return [];
        }

        if (empty($sMethod)) {
            $sMethod = self::METHOD_POST;
        }

        $arRequest = [
            'json'        => $arRequest,
            'http_errors' => false,
            'headers'     => [],
        ];

        if ($bAuth) {
            if (!empty($this->sTokenExpirationDate)
                && Argon::now()->toDateTimeString() >= $this->sTokenExpirationDate
            ) {
                $this->refreshToken();
            }
            if (!empty($this->sToken)) {
                $arRequest['headers']['Authorization'] = 'Bearer '.$this->sToken;
            }
        }

        try {
            $obClient = new Client();
            $obResponse = $obClient->request($sMethod, $sUrl, $arRequest);
        } catch (\Exception $obException) {
            LogHelper::error($sClassMethod, $obException->getMessage(), $arRequest);

            return [];
        }

        if ($obResponse->getStatusCode() != 200) {
            $arResponse = json_decode($obResponse->getBody()->getContents(), true);
            LogHelper::error($sClassMethod, '', $arRequest, $arResponse);

            return [];
        }

        $arResponse = json_decode($obResponse->getBody()->getContents(), true);

        return $arResponse;
    }

    /**
     * Save token.
     * @param array $arResponse
     */
    protected function saveToken($arResponse = [])
    {
        if (!is_array($arResponse)) {
            return;
        }

        $this->sToken        = array_get($arResponse, 'access_token');
        $this->sRefreshToken = array_get($arResponse, 'refresh_token');
        $iExpiresIn          = (int) array_get($arResponse, 'expires_in');

        if (!empty($this->sToken) && !empty($this->sRefreshToken) && !empty($iExpiresIn)) {
            $obTokenExpirationDate = clone Argon::now();
            $this->sTokenExpirationDate = $obTokenExpirationDate->addSeconds($iExpiresIn)->toDateTimeString();

            AmoCrmSettings::set('form', 0);
            AmoCrmSettings::set('access_token', $this->sToken);
            AmoCrmSettings::set('token_expiration_date', $this->sTokenExpirationDate);
            AmoCrmSettings::set('refresh_token', $this->sRefreshToken);
        }
    }
}
