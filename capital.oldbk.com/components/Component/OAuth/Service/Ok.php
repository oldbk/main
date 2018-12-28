<?php

namespace components\Component\OAuth\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\OAuth2\Service\AbstractService;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * Class Ok
 * @package components\OAuth\Service
 *
 */
class Ok extends AbstractService
{
    const SCOPE_VALUABLE = 'VALUABLE ACCESS';

    protected $uids = array();
    protected $fields = array();
    /** @var \components\Component\OAuth\Common\Consumer\Credentials */
    protected $credentials;

    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('http://api.ok.ru/fb.do');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://connect.ok.ru/oauth/authorize');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://api.odnoklassniki.ru/oauth/token.do');
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        $token->setLifeTime($data['expires_in']);

        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token']);
        unset($data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_QUERY_STRING;
    }

    /**
     * @return string
     */
    public function getUids()
    {
        return $this->uids;
    }

    /**
     * @param string $uids
     *
     * @return $this
     */
    public function setUids($uids)
    {
        $this->uids = $uids;
        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function addField($field)
    {
        if(!in_array($field, $this->fields))
            $this->fields[] = $field;

        return $this;
    }

    public function request($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        $token = $this->storage->retrieveAccessToken($this->service());

        $request_params = array(
            'application_key'   => $this->credentials->getConsumerPublicKey(),
            'format'            => 'json',
            'method'            => $path,
        );
        if($this->uids)
            $request_params['uids'] = implode(',', $this->uids);

        $request_params = array_merge($request_params, is_array($body) ? $body : array());
        $request_params['sig'] = $this->calcSignature($request_params, $token->getAccessToken());

        foreach ($request_params as $key => $value)
            $this->baseApiUri->addToQuery($key, $value);

        return parent::request('/', $method, null, $extraHeaders);
    }

    private function calcSignature(array $parameters, $access_token)
    {
        if (!ksort($parameters)){
            return null;
        } else {
            $requestStr = "";
            foreach($parameters as $key=>$value){
                $requestStr .= $key . "=" . $value;
            }
            $requestStr .= md5($access_token.$this->credentials->getConsumerSecret());

            return md5($requestStr);
        }
    }
}
