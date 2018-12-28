<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 09.06.2016
 */

namespace components\Component\Quests\object;


use components\Component\Quests\pocket\itemInfo\iRewardItem;
use components\Component\Quests\pocket\itemInfo\iTakeItem;

class Take extends Base
{
    public $pocket_item_id;
    public $pocket_id;
    public $quest_id;
    public $item_type;

    public $item_id;

    public $count = 0;

    /** @var iTakeItem */
    public $info;

    /**
     * @return mixed
     */
    public function getPocketItemId()
    {
        return $this->pocket_item_id;
    }

    /**
     * @return mixed
     */
    public function getQuestId()
    {
        return $this->quest_id;
    }

    /**
     * @return mixed
     */
    public function getItemType()
    {
        return $this->item_type;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return iRewardItem
     */
    public function getInfo()
    {
        return $this->info;
    }
}