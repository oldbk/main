<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.10.2015
 */

namespace components\Controller\_base;

use components\Component\Db\CapitalDb;
use components\Component\Slim\Slim;
use components\models\OAuthUser;
use components\models\SnSettings;
use components\models\SnTemp;
use components\models\User;
use components\models\UserSn;
use OAuth\OAuth2\Service\ServiceInterface;

abstract class OAuthController extends BaseController
{
    protected $layout = 'message';

    /** @var ServiceInterface */
    protected $service;

    protected $user;

    protected $settings = [];

    abstract protected function getCredentials();

    /**
     * @return string
     */
    abstract protected function getServiceName();

    /**
     * @return int
     */
    abstract protected function getServiceId();

    /**
     * @return boolean
     */
    abstract protected function isDefault();

    /**
     * @param string $code
     *
     * @return boolean|OAuthUser
     */
    abstract protected function getUserInfo($code);

    /**
     * @return array
     */
    abstract protected function getScopes();

    /**
     * @param \OAuth\ServiceFactory $serviceFactory
     */
    abstract protected function registerService(&$serviceFactory);

    public function __construct(Slim $app, $action)
    {
        $SnSettings = SnSettings::where('name', '=', $this->getServiceName())->first();
        if(!$SnSettings) {
			throw new \Exception('Not found 404');
		}
        $this->settings = $SnSettings->toArray();

         parent::__construct($app, $action);
    }

    protected function beforeAction($action)
    {
        $this->setService($this->prepareService());
        if($user_uid = $this->get('session')->get('uid')) {
            $this->user = User::find($user_uid)->toArray();
        }

        return parent::beforeAction($action);
    }

    public function indexAction()
    {
        $view = 'oauth/message';
        $message = null;
        try {
            $url = $this->getService()->getAuthorizationUri();
            //если это привязка соц. сети к персонажу и у этого персонажа уже есть привязаная эта соц. сеть, кидаем на ошибку
            if($this->get('request')->get('callback') == 'assign' && $this->checkAssignExist($this->user['id']) === true)
                throw new \Exception('Эта социальная сеть уже привязана к Вам');

            $this->redirect($url->getAbsoluteUri());
        } catch (\Exception $ex) {
            $view = 'oauth/message';
            $message = $ex->getMessage();
        }

        $this->render($view, array(
            'message' => $message,
            'sn_type' => $this->getServiceId()
        ));
    }

    public function successAction()
    {
        $message = null;
        $User = null;
        try {
            if(($code = $this->get('request')->get('code')) === null) {
                throw new \Exception('Страница не найдена', 404);
            }

            //получаем данные из соц. сети
            if(($User = $this->getUserInfo($code)) === false) {
                throw new \Exception('Страница не найдена', 404);
            }
        } catch (\Exception $ex) {
            $message = $ex->getMessage();

            $this->render('oauth/message', array(
                'message' => $message,
                'sn_type' => $this->getServiceId()
            ));
            return;
        }


        //делаем редирект либо на авторизацию, либо на регистрацию
        $this->doRedirect($User);
    }

    public function assignAction()
    {
        try {
            if(($code = $this->get('request')->get('code')) === null)
                throw new \Exception('Привязать социальную сеть не удалось', 404);

            //получаем данные пользователя из соц. сети
            if(($User = $this->getUserInfo($code)) === false)
                throw new \Exception('Привязать социальную сеть не удалось', 404);

            //проверяем есть ли уже такая привязка
            if(($user = $this->checkSNExist($User)) !== false) {
                if($user['id'] == $this->user['id'])
                    throw new \Exception('Эта социальная сеть уже привязана к Вам', 404);
                else
                    throw new \Exception('Этот аккаунт социальной сети уже привязан к другому персонажу', 404);
            }

            //добавляем запись о привязке
            $User->setUserId($this->user['id']);
            if(!$this->insertSN($User))
                throw new \Exception('Привязать социальную сеть не удалось', 404);

            $view = 'oauth/message';
            $message = 'Социальная сеть привязана';
        } catch (\Exception $ex) {
            $view = 'oauth/message';
            $message = $ex->getMessage();
        }

        $this->render($view, array(
            'message' => $message,
            'sn_type' => $this->getServiceId()
        ));
    }

    protected function getSettings($field)
    {
        return isset($this->settings[$field]) ? $this->settings[$field] : null;
    }

    /**
     * @return ServiceInterface
     */
    protected function getService()
    {
        return $this->service;
    }

    /**
     * @param ServiceInterface $service
     *
     * @return $this
     */
    protected function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    protected function checkSNExist(OAuthUser $User)
    {

		$UserSn = UserSn::where('sn_type', '=', $User->getSnType())
			->where('sn_id', '=', $User->getSnId())
			->whereRaw('is_deleted = 0')
			->first();

        if(!$UserSn) {
			return false;
		}

		return User::find($UserSn->user_id)->toArray();
    }

    protected function checkAssignExist($user_id)
    {
		$count = UserSn::where('sn_type', '=', $this->getServiceId())
			->where('user_id', '=', $user_id)
			->whereRaw('is_deleted = 0')
			->count();

        return $count > 0 ? true : false;
    }

    /**
     * @param OAuthUser $User
     */
    protected function doRedirect($User)
    {
        if(($user = $this->checkSNExist($User)) === false) {
            $sid = md5($User->getSnId().$User->getSnType().md5($User->getSnId().$User->getSnType().time()));

            SnTemp::firstOrCreate([
            	'sn_id' => $User->getSnId(),
				'sn_type' => $User->getSnType(),
			], [
				'gender'        => $User->getGender(),
				'birthday'      => $User->getBirthday() ? $User->getBirthday()->getTimestamp() : 0,
				'email'         => $User->getEmail(),
				'created_at'    => time(),
				'sid'           => $sid,
			]);

            $this->redirect($this->app->config('url.oldbk').'/f/reg?sid='.$sid);
        }

        $User->setLogin($user['login']);
        $this->get('session')->set('sn_user', $User->__toArray());

        $this->redirect('https://oldbk.com/f/login');
    }

	/**
	 * @param OAuthUser $User
	 * @return bool|int
	 */
    protected function insertSN(OAuthUser $User)
    {
        if(!$User->getUserId())
            return false;

        $data = [
			'user_id'       => $User->getUserId(),
			'sn_type'       => $User->getSnType(),
			'sn_id'         => $User->getSnId(),
			'created_at'    => time(),
		];

        return UserSn::insertGetId($data);
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    protected function getCallbackUrl()
    {
        if(($callback = $this->get('request')->get('callback')) !== null) {
            $params = array(
                'action' => $callback,
                'callback' => $callback
            );
        } else
            $params = array('action' => 'success');

        return sprintf('%s%s',
            $this->app->config('url.capital'), $this->app->urlFor('oauth.'. $this->getServiceName(), $params));
    }

    /**
     * @return ServiceInterface
     */
    protected function prepareService()
    {
        /** @var $serviceFactory \OAuth\ServiceFactory An OAuth service factory. */
        $serviceFactory = new \OAuth\ServiceFactory();
        $serviceFactory->setHttpClient(new \OAuth\Common\Http\Client\CurlClient());
        if(!$this->isDefault())
            $this->registerService($serviceFactory);

        // Session storage
        $storage = new \OAuth\Common\Storage\Session();

        return $serviceFactory->createService($this->getServiceName(), $this->getCredentials(), $storage, $this->getScopes());
    }
}