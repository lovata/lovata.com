<?php namespace System\Classes\UpdateManager;

use Url;
use Lang;
use Http;
use Config;
use Request;
use System\Models\Parameter;
use System\Models\PluginVersion;
use ApplicationException;
use Exception;

/**
 * HasGatewayAccess
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasGatewayAccess
{
    /**
     * @var string Secure API Key
     */
    protected $key;

    /**
     * @var string Secure API Secret
     */
    protected $secret;

    /**
     * requestServerData contacts the update server for a response.
     * @param  string $uri
     * @param  array  $postData
     * @return array
     */
    public function requestServerData($uri, $postData = [])
    {
        $result = $this->makeHttpRequest($this->createServerUrl($uri), $postData);
        $contents = $result->body();

        if ($result->status() === 404) {
            throw new ApplicationException(Lang::get('system::lang.server.response_not_found'));
        }

        if ($result->status() !== 200) {
            throw new ApplicationException(
                strlen($contents)
                ? $contents
                : Lang::get('system::lang.server.response_empty')
            );
        }

        $resultData = false;

        try {
            $resultData = @json_decode($contents, true);
        }
        catch (Exception $ex) {
            throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
        }

        if ($resultData === false || (is_string($resultData) && !strlen($resultData))) {
            throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
        }

        return $resultData;
    }

    /**
     * Set the API security for all transmissions.
     * @param string $key    API Key
     * @param string $secret API Secret
     */
    public function setSecurity($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * createServerUrl creates a complete gateway server URL from supplied URI
     * @param string $uri URI
     * @return string URL
     */
    protected function createServerUrl($uri)
    {
        $gateway = Config::get('system.update_gateway', 'https://gateway.octobercms.com/api');
        if (substr($gateway, -1) !== '/') {
            $gateway .= '/';
        }

        return $gateway . $uri;
    }

    /**
     * makeHttpRequest makes a specialized server request to a URL.
     * @param string $url
     * @param array $postData
     * @return \Illuminate\Http\Client\Response
     */
    protected function makeHttpRequest($url, $postData)
    {
        // New HTTP instance
        $http = Http::asForm();
        $headers = [];

        // Post data
        $postData['protocol_version'] = '2.0';
        $postData['client'] = 'October CMS';
        $postData['server'] = base64_encode(json_encode([
            'php' => PHP_VERSION,
            'url' => Url::to('/'),
            'ip' => Request::ip(),
            'since' => PluginVersion::orderBy('created_at')->value('created_at')
        ]));

        // Include project key if available
        if ($projectKey = Parameter::get('system::project.key')) {
            $postData['project'] = $projectKey;
        }

        // Signed request
        if ($this->key && $this->secret) {
            $postData['nonce'] = $this->createNonce();
            $headers['Rest-Key'] = $this->key;
            $headers['Rest-Sign'] = $this->createSignature($postData, $this->secret);
        }

        // Gateway auth
        if ($credentials = Config::get('system.update_gateway_auth')) {
            if (is_string($credentials)) {
                $credentials = explode(':', $credentials);
            }

            list($user, $pass) = $credentials;
            $http->withBasicAuth($user, $pass);
        }

        // Attach headers
        if ($headers) {
            $http->withHeaders($headers);
        }

        return $http->post($url, $postData);
    }

    /**
     * Create a nonce based on millisecond time
     * @return int
     */
    protected function createNonce()
    {
        $mt = explode(' ', microtime());
        return $mt[1] . substr($mt[0], 2, 6);
    }

    /**
     * Create a unique signature for transmission.
     * @return string
     */
    protected function createSignature($data, $secret)
    {
        return base64_encode(hash_hmac('sha512', http_build_query($data, '', '&'), base64_decode($secret), true));
    }
}
