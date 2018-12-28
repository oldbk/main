<?php
namespace components\Controller;
use components\Component\Security\TwoFA;
use \components\Controller\_base\MainController;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 *
 */
class SecurityController extends MainController
{
	public $layout = 'security';

	public function beforeAction($action)
	{
		if(!in_array($action, ['resetPassword'])) {
			$r = parent::beforeAction($action);

			return $r;
		}

		return true;
	}

    public function twofaAction()
    {
		$Security = new TwoFA();
		if(!$Security->isNeedVerify()) {
			$this->redirect($Security->getRef());
		}

		$code = isset($_POST['2fa_code']) ? $_POST['2fa_code'] : null;
		if($code !== null) {
			$valid = $Security->verify($this->app->webUser->getId(), $code);
			if($valid) {
				$this->redirect($Security->getRef());
			}
		}

		$this->render('twofa');
    }

    public function resetPasswordAction()
	{
		$this->render('resetPassword');
	}
}