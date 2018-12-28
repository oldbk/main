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
 * Class OkController
 * @package components\Controller\oauth
 *
 * @method \components\Component\OAuth\Service\Ok getService()
 */
class OkController extends OAuthController
{
    protected function getCredentials()
    {
        return new \components\Component\OAuth\Common\Consumer\Credentials(
            $this->getSettings('key'),
            $this->getSettings('public_key'),
            $this->getSettings('secret_key'),
            $this->getCallbackUrl()
        );
    }

    protected function getServiceName()
    {
        return 'ok';
    }

    protected function getServiceId()
    {
        return OAuthUser::SN_ODNOKLASSNIKI;
    }

    protected function registerService(&$serviceFactory)
    {
        $serviceFactory->registerService('ok', '\\components\\Component\\OAuth\\Service\\Ok');
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
        $response = array();
        try {
            // This was a callback request from Amazon, get the token
            $token = $this->getService()->requestAccessToken($code);

            // Retrieve a token and send a request
            $response = json_decode($this->getService()->request('users.getCurrentUser', 'GET'), true);
        } catch (\Exception $ex) {

        }

        if(!isset($response['uid']))
            return false;

        $User = new OAuthUser();
        $User->setGender($response['gender'] == 'male' ? OAuthUser::GENDER_MALE : OAuthUser::GENDER_FEMALE)
            ->setSnType(OAuthUser::SN_ODNOKLASSNIKI)
            ->setSnId($response['uid']);
        if(isset($response['birthday'])) {
            $datetime = new \DateTime();
            $datetime->setTimestamp(strtotime(sprintf('%s 00:00:00', $response['birthday'])));
            $User->setBirthday($datetime);
        }

        return $User;
    }
}