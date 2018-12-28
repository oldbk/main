<?php

namespace components\Helper;


use components\Component\Slim\Slim;
use components\Eloquent\User;

/**
 * Class Auth
 * @package components\Helper
 */
class Auth
{
    /**
     * @var User $user
     */
    protected $user;

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $app = Slim::getInstance();

        $user = null;

        if ($app->session->get('KO_login')) {
            $app->session->delete('uid');
        }

        if ($uid = $app->session->get('uid', false)) {
            $user = User::find($uid);
        }

        return $this->user = $user;
    }

    /**
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

}