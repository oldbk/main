<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\validator;

use components\models\User;

interface iValidator
{
    /**
     * @deprecated
     * @return mixed
     */
    public function getCheckerTypes();

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

    public function populate(array $attributes);

    public function check($Checker);
}