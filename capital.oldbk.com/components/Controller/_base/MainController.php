<?php
namespace components\Controller\_base;
use components\Component\Slim\Slim;
use components\models\Bank;
use components\models\User;

/**
 * Created by PhpStorm.
 */

abstract class MainController extends BaseController
{
    /** @var User */
    protected $user;

    protected $black_ip = array(
        '77.120.192.136',
        '188.19.171.169',
        '84.52.34.215'
    );
    protected $is_block = true;

    public function __construct(Slim $app, $action)
    {
        parent::__construct($app, $action);
    }

    private function getBlackList()
    {
        return $this->black_ip;
    }

    public function beforeAction($action)
    {
        $r = parent::beforeAction($action);

        //черный список Ip адресов
        if(in_array($this->app->request()->getIp(), $this->getBlackList())) {
            $this->errorUser();
        }

        //блок в сессии
        if($this->get('session')->get('block')) {
            $this->get('session')->destroy();
            $this->errorUser();
        }

        $this->getUser();
        $this->checkUser();

        return $r;
    }

    protected function getUser()
    {
        $this->user = $this->app->webUser->getUser();
    }

    protected function checkUser()
    {
        if(!$this->user) {
            $this->errorUser();
        }

        if($this->user->block) {
            $this->get('session')->destroy();
            $this->errorUser();
        }

        if($this->user->sid != $this->app->session->get('sid')) {
            $this->app->redirect('/index.php');
        }

        if($bank_id = $this->app->session->get('bank_auth')) {
            $bank = Bank::whereRaw('id = ? and owner = ?', [$bank_id, $this->user->id])->first();
            if($bank) {
				$this->user->bank = $bank->toArray();
			}
        }
    }

    protected function errorUser()
    {
        echo "<html><head><META http-equiv=Content-type content='text/html; charset=windows-1251'><title>Произошла ошибка</title></head><body><BR>Произошла ошибка!<BR>Неверный пароль, войдите с <a href=index.php>главной страницы</a>.<BR><BR><BR><hr><table width=100%><tr><td align=left><b><a href='javascript:window.history.go(-1);'>Назад</a></b></td><td align=right>(C) OldBK</td></tr></table></body></html>";
        die();
    }
}