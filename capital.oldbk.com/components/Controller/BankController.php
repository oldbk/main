<?php
namespace components\Controller;
use \components\Controller\_base\MainController;
use components\models\Bank;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 *
 */
class BankController extends MainController
{
    public function loginAction()
    {
        if($this->app->session->get('bank_auth')) {
            $this->app->flash('error', 'Вы уже авторизованы в банке');
            $this->redirect($this->app->urlFor('znahar', array('action' => 'index')));
        }

        $bank_id = $this->app->request->post('number');
        $password = $this->app->request->post('password');
        if($bank_id === null || $password === 'null') {
            $this->app->flash('error', 'Ошибка входа');
            $this->redirect($this->app->urlFor('znahar', array('action' => 'index')));
        }
        $Bank = Bank::login($this->user->id, $bank_id, $password);
        if(!$Bank) {
            $this->app->flash('error', 'Ошибка входа');
            $this->redirect($this->app->urlFor('znahar', array('action' => 'index')));
        }

        $this->app->session->set('bank_auth', $Bank['id']);

        $this->app->flash('success', 'Вы успешно авторизовались в банке');

        $this->redirect($this->app->request->getReferer());
    }

    public function logoutAction()
    {
        $this->app->session->delete('bank_auth');
        $this->redirect($this->app->request->getReferer());
    }
}