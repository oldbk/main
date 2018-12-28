<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\validator;


use components\models\User;

abstract class BaseValidator implements iValidator
{
    const ITEM_TYPE_FIGHT       = 'fight';
    const ITEM_TYPE_LOCATION    = 'location';
    const ITEM_TYPE_USER        = 'user';
    const ITEM_TYPE_GAME_ENTER  = 'gameEnter';

    public $process = array();

    /** @deprecated */
    protected $user;

    public function setProcess($process)
    {
        $this->process = $process;
        return $this;
    }

    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param $type
     * @return iValidator
     */
    public static function getValidator($type)
    {
        $type = str_replace(' ', '', ucwords(str_replace('_', ' ', $type)));
        $className = sprintf('components\Component\Quests\validator\\Validator%s', ucfirst($type));
        try {
            return new $className();
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function populate(array $attributes)
    {
        foreach ($attributes as $field => $value) {
            if(property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }
}