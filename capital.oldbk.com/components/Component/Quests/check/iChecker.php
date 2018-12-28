<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;


use components\models\Battle;
use components\models\User;

interface iChecker
{
    public function getCheckerType();

    /**
     * @param $process
     * @return self
     */
    public function setProcess($process);
    public function getProcess();

    /**
     * @param User $user
     * @return self
     */
    public function setUser($user);

    /**
     * @return User
     */
    public function getUser();

    /**
     * @return Battle
     */
    public function getBattle();

    /**
     * @param $battle
     * @return Battle|null
     */
    public function setBattle($battle);
}