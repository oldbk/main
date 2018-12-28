<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;

use components\models\Battle;
use components\models\User;

abstract class BaseChecker implements iChecker
{
    const ITEM_TYPE_DROP            = 'drop';
    const ITEM_TYPE_ITEM            = 'item';
    const ITEM_TYPE_FIGHT           = 'fight';
    const ITEM_TYPE_GIFT            = 'gift';
    const ITEM_TYPE_MAGIC           = 'magic';
    const ITEM_TYPE_EVENT           = 'event';
    const ITEM_TYPE_KILL_BOT        = 'kill_bot';
    const ITEM_TYPE_HILL            = 'hill';
    const ITEM_TYPE_WEIGHT          = 'weight';
    const ITEM_TYPE_EMPTY           = 'empty';

    public $process = array();
    public $count;

    /** @var User */
    protected $user;
    /** @var Battle */
    public $battle;

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
     * @return Battle
     */
    public function getBattle()
    {
        return $this->battle;
    }

    /**
     * @param Battle $battle
     * @return $this
     */
    public function setBattle($battle)
    {
        $this->battle = $battle;
        return $this;
    }

    public static function getAllTypes()
    {
        return array(
            self::ITEM_TYPE_DROP,
            self::ITEM_TYPE_ITEM,
            self::ITEM_TYPE_FIGHT,
            self::ITEM_TYPE_GIFT,
            self::ITEM_TYPE_MAGIC,
            self::ITEM_TYPE_EVENT,
            self::ITEM_TYPE_KILL_BOT,
            self::ITEM_TYPE_HILL,
            self::ITEM_TYPE_WEIGHT,
            self::ITEM_TYPE_EMPTY,
        );
    }
}