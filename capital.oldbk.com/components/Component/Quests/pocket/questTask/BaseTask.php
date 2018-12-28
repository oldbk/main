<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\pocket\iPocketItemInfo;
use components\models\User;

abstract class BaseTask implements iPocketItemInfo, iQuestTask
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
    const ITEM_TYPE_BUY             = 'buy';

    public $process = array();
    public $user;
    public $up_count = 1;

    public function getProcess()
    {
        return $this->process;
    }

    public function setProcess($process)
    {
        $this->process = $process;
        return $this;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getUpCount()
    {
        return $this->up_count;
    }

    /**
     * @param int $up_count
     */
    public function setUpCount($up_count)
    {
        $this->up_count = $up_count;
    }

    /**
     * @param $type
     * @return iPocketItemInfo|iQuestTask
     */
    public static function getQuestTask($type)
    {
        $type = str_replace(' ', '', ucwords(str_replace('_', ' ', $type)));
        $className = sprintf('components\Component\Quests\pocket\questTask\\%sTask', ucfirst($type));
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

    public function getCount()
    {
        return 0;
    }
}