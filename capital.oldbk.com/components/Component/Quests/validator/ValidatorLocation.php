<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 27.08.16
 * Time: 23:18
 */

namespace components\Component\Quests\validator;


use components\Component\Quests\check\BaseChecker;
use components\Component\Quests\check\CheckerEvent;
use components\Component\Quests\check\iChecker;
use components\models\Battle;
use components\models\User;

class ValidatorLocation extends BaseValidator
{
    public $room;

    public function getCheckerTypes()
    {
        return BaseChecker::getAllTypes();
    }

    /**
     * @param iChecker|CheckerEvent $Checker
     * @return bool
     */
    public function check($Checker)
    {
        $user = $Checker->getUser();
        if(is_numeric($this->room) && $this->room != $user->room) {
            return false;
        }

        if(is_string($this->room) && method_exists($this, $this->room.'Room')) {
            $method = $this->room.'Room';

            return $this->{$method}($user);
        }

        return true;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function bsRoom($user)
    {
        return $user->room > 10000 && $user->room < 11000;
    }

    public function zagaRoom($user)
    {
        return ($user->room > 50000 && $user->room < 53600 || $user->room > 53601 && $user->room < 53699);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function ruineRoom($user)
    {
        return $user->room >= 1000 && $user->room <= 10000;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function lab_1Room($user)
    {
        return $user->lab == 1;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function lab_2Room($user)
    {
        return $user->lab == 2;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function lab_3Room($user)
    {
        return $user->lab == 3;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function lab_4Room($user)
    {
        return $user->lab == 4;
    }
}