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
 * Class FacebookController
 * @package components\Controller\oauth
 *
 * @method \OAuth\OAuth2\Service\Facebook getService()
 */
class FacebookController extends OAuthController
{
    protected function getCredentials()
    {
        return new \OAuth\Common\Consumer\Credentials(
            $this->getSettings('key'),
            $this->getSettings('secret_key'),
            $this->getCallbackUrl()
        );
    }

    protected function getServiceName()
    {
        return 'facebook';
    }

    protected function getServiceId()
    {
        return OAuthUser::SN_FACEBOOK;
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
            // This was a callback request from facebook, get the token
            $token = $this->getService()->requestAccessToken($code, $this->get('request')->get('state'));

            $data = $this->getService()->request('/me?fields=birthday,email,gender,name');
            // Send a request with it
            $result = json_decode($data, true);
        } catch (\Exception $ex) {

        }

        if(!isset($result['id']))
            return false;

        $User = new OAuthUser();
        $User->setGender($result['gender'] == 'male' ? OAuthUser::GENDER_MALE : OAuthUser::GENDER_FEMALE)
            ->setSnType(OAuthUser::SN_FACEBOOK)
            ->setSnId($result['id']);

        return $User;
    }
}