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
 * Class MailruController
 * @package components\Controller\oauth
 *
 * @method \components\Component\OAuth\Service\Mailru getService()
 */
class MailruController extends OAuthController
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
        return 'mailru';
    }

    protected function getServiceId()
    {
        return OAuthUser::SN_MAILRU;
    }

    protected function registerService(&$serviceFactory)
    {
        $serviceFactory->registerService('mailru', '\\components\\Component\\OAuth\\Service\\Mailru');
    }

    protected function isDefault()
    {
        return false;
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
            $result = json_decode($this->getService()->request('users.getInfo'), true);
        } catch (\Exception $ex) {

        }

        if(!isset($result[0]['uid']))
            return false;
        $response = $result[0];

        $User = new OAuthUser();
        $User->setGender($response['sex'] == 0 ? OAuthUser::GENDER_MALE : OAuthUser::GENDER_FEMALE)
            ->setSnType(OAuthUser::SN_MAILRU)
            ->setSnId($response['uid']);
        if(isset($response['birthday'])) {
            $temp = explode('.', $response['birthday']);
            $timestamp = strtotime(sprintf('%s-%s-%s 00:00:00', $temp[2], $temp[1], $temp[0]));
            $datetime = new \DateTime();
            $datetime->setTimestamp($timestamp);
            $User->setBirthday($datetime);
        }
        if(isset($response['email']))
            $User->setEmail($response['email']);

        return $User;
    }
}