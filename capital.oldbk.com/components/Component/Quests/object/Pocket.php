<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.06.2016
 */

namespace components\Component\Quests\object;

class Pocket extends Base
{
    public $id;
    public $quest_id;
    public $item_id;
    public $item_type;
    /** @var Task[]|Reward[]|Take[] */
    public $items = array();
    public $condition;
    public $dialog_finish_id;

    /**
     * @param Task|Reward|Take $item
     * @return $this
     */
    public function addItem($item)
    {
        $this->items[$item->getPocketItemId()] = $item;
        return $this;
    }

    public function getRandItem()
    {
        $keys = array_keys($this->items);
        $key = $keys[rand(0, count($keys) - 1)];

        return $this->items[$key];
    }

    public function isAllFinished()
    {
        $finished = true;
        foreach ($this->items as $item) {
            if(!$item->isFinished()) {
                $finished = false;
                break;
            }
        }

        return $finished;
    }
}