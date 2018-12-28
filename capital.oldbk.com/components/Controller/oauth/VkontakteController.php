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
 * Class VkontakteController
 * @package components\Controller\oauth
 *
 * @method \OAuth\OAuth2\Service\Vkontakte getService()
 */
class VkontakteController extends OAuthController
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
        return 'vkontakte';
    }

    protected function getServiceId()
    {
        return OAuthUser::SN_VKONTAKTE;
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
            // This was a callback request from Amazon, get the token
            $token = $this->getService()->requestAccessToken($code);

            // Retrieve a token and send a request
            $result = json_decode($this->getService()->request('users.get', 'POST', array('fields' => 'sex,bdate,verified')), true);
        } catch (\Exception $ex) {

        }

        if(!isset($result['response'][0]['uid']))
            return false;
        $response = $result['response'][0];

        $User = new OAuthUser();
        $User->setGender($response['sex'] == 2 ? OAuthUser::GENDER_MALE : OAuthUser::GENDER_FEMALE)
            ->setSnType(OAuthUser::SN_VKONTAKTE)
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