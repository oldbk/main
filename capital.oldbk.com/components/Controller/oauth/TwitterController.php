<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */

namespace components\Controller\oauth;

use components\Controller\_base\OAuthController;
use components\models\OAuthUser;

/**
 * Class TwitterController
 * @package components\Controller\oauth
 *
 * @method \OAuth\OAuth1\Service\Twitter getService()
 */
class TwitterController extends OAuthController
{
    protected function getCredentials()
    {
        return new \OAuth\Common\Consumer\Credentials(
            $this->getSettings('key'),
            $this->getSettings('secret_key'),
            $this->getCallbackUrl()->getAbsoluteUri()
        );
    }

    protected function getServiceName()
    {
        return 'twitter';
    }

    protected function getServiceId()
    {
        return OAuthUser::SN_TWITTER;
    }

    protected function registerService(&$serviceFactory)
    {

    }

    protected function isDefault()
    {
        return true;
    }

    protected function getScopes()
    {
        return array();
    }

    protected function getUserInfo($code)
    {
        $result = array();
        try {
            $token = $this->getService()->getStorage()->retrieveAccessToken('Twitter');

            // This was a callback request from twitter, get the token
            $this->getService()->requestAccessToken(
                $this->request->get('oauth_token'),
                $this->request->get('oauth_verifier'),
                $token->getRequestTokenSecret()
            );

            // Send a request now that we have access token
            $result = json_decode($this->getService()->request('account/verify_credentials.json'));
            var_dump($result);die;

            echo 'result: <pre>' . print_r($result, true) . '</pre>';
        } catch (\Exception $ex) {

        }

        if(!isset($result['response'][0]['uid']))
            return false;
        $response = $result['response'][0];

        $User = new OAuthUser();
        $User->setGender($response['sex'] == 2 ? OAuthUser::GENDER_MALE : OAuthUser::GENDER_FEMALE)
            ->setSnType(OAuthUser::SN_TWITTER)
            ->setSnId($response['uid']);
        if($response['bdate']) {
            $temp = explode('.', $response['bdate']);
            $timestamp = strtotime(sprintf('%s-%s-%s 00:00:00', $temp[2], $temp[1], $temp[0]));
            $datetime = new \DateTime();
            $datetime->setTimestamp($timestamp);
            $User->setBirthday($datetime);
        }

        return $User;
    }
}