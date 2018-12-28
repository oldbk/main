<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.06.2016
 */

namespace components\Component\Quests\object;

use components\Component\Quests\check\CheckerEmpty;
use components\Component\Quests\pocket\itemInfo\iRewardItem;
use components\Component\Quests\pocket\questTask\BaseTask;
use components\models\Chat;
use components\models\quest\QuestList;
use components\models\quest\QuestPart;
use components\models\quest\QuestPocket;
use components\models\User;

class Part extends Base
{
    public $quest_id;
    public $id;
    public $name;
    public $img;
    public $description_type;
    public $description_data;
    public $chat_start;
    public $chat_end;
	public $is_auto_start = 0;
    public $is_auto_finish = false;
    public $part_number;
    public $is_finished = false;
    public $is_started = 0;
    public $complete_condition_message;

    public $user_quest_id;
    public $user_part_id;

    /** @var Pocket[] */
    public $task = array();
    /** @var Pocket[] */
    public $reward = array();
    /** @var Pocket[] */
    public $take = array();

    public function addTask(Pocket $Task)
    {
        $this->task[] = $Task;
        return $this;
    }

    public function addReward(Pocket $Reward)
    {
        $this->reward[] = $Reward;
        return $this;
    }

    public function addTake(Pocket $Take)
    {
        $this->take[] = $Take;
        return $this;
    }

    /**
     * @param array $exclude_ids
     * @return bool
     */
    public function isReadyToFinish($exclude_ids = [])
    {
        foreach ($this->task as $PocketTask) {
            $valid = 0;
            foreach ($PocketTask->items as $Task) {
                if($Task->isFinished() || in_array($Task->getUserTaskId(), $exclude_ids)) {
                    $valid++;
                }
            }

            if($PocketTask->condition == QuestPocket::TYPE_AND && $valid != count($PocketTask->items)) {
                return false;
            }

            if($PocketTask->condition == QuestPocket::TYPE_OR && $valid == 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @var iRewardItem[]
     */
    private $_reward_list = array();
    /**
     * @param User $owner
     * @return bool
     */
    public function giveReward($owner)
    {
        $Checker = new CheckerEmpty();
        $Checker->setUser($owner);

        foreach ($this->reward as $PocketReward) {
			$items = [];
			foreach ($PocketReward->items as $Reward) {
				if ($Reward->check($Checker) === false) {
					continue;
				}

				$items[] = $Reward;
			}

            switch (true) {
                case ($PocketReward->condition == QuestPocket::TYPE_AND):
                    foreach ($items as $Reward) {
                        if($Reward->info->give($owner, $this, $Reward) == false) {
                            return false;
                        }

						$this->_reward_list[$Reward->pocket_id] = $Reward->info->getName();
                    }

                    break;
                case ($PocketReward->condition == QuestPocket::TYPE_OR):
                    shuffle($items);
                    foreach ($items as $Reward) {
                        if($Reward->info->give($owner, $this, $Reward) == false) {
                            return false;
                        }

                        $this->_reward_list[$Reward->pocket_id] = $Reward->info->getName();
                        break;
                    }

                    break;
            }
        }

        return true;
    }

    /**
     * @param User $owner
     * @return bool
     */
    public function take($owner)
    {
        foreach ($this->take as $PocketTake) {
            foreach ($PocketTake->items as $Take) {
                if($Take->info->take($owner, $this, $Take) == false) {
                    return false;
                }
            }
        }

        return true;
    }

    public function sendStartMessage($owner)
    {
        if(!$this->chat_start) {
            return true;
        }
        $message = $this->prepareMessage($this->chat_start);

        return Chat::addToChatSystem($message, $owner);
    }

    public function sendFinishedMessage($owner)
    {
        if(!$this->chat_end) {
            return true;
        }
        $message = $this->prepareMessage($this->chat_end);

        return Chat::addToChatSystem($message, $owner);
    }

    public function sendCompleteMessage($owner)
    {
        if(!$this->complete_condition_message) {
            return true;
        }
        $message = $this->prepareMessage($this->complete_condition_message);

        return Chat::addToChatSystem($message, $owner);
    }

    private function prepareMessage($message)
    {
        $process_link = '<a href="javascript:void(0)" onclick="top.cht(\'http://capitalcity.oldbk.com/main.php?effects=1&edit=#quests\');" title="Состояние">Состояние</a>';
        $Quest = QuestList::find($this->quest_id)->toArray();

        $interval = ($Quest['limit_interval'] > 24) ? ($Quest['limit_interval'] / 24). ' дн.' : ($Quest['limit_interval']). ' ч.';

        if($this->_reward_list) {
            $keys = array_map(function($value){ return '{reward:'.$value.'}';}, array_keys($this->_reward_list));
            $message = str_replace($keys, array_values($this->_reward_list), $message);
        }

        return str_replace(
            array('%process%', "'", "\n", "\r\n", "\r", '%interval%'),
            array($process_link, "\\\'", " ", " ", " ", $interval),
            $message);
    }

    public function getDescription()
    {
        switch ($this->description_type) {
            case QuestPart::DESCRIPTION_TYPE_TASK:
                return $this->getDescriptionFormTask();
                break;
            case QuestPart::DESCRIPTION_TYPE_INVENTORY:
                return $this->getDescriptionFromInventory();
                break;
        }

        return null;
    }

    protected function getDescriptionFormTask()
    {
        $message = $this->description_data;
        foreach ($this->task as $PocketTask) {
            $pocket = 0;
            $pocket_need = 0;

            foreach ($PocketTask->items as $Task) {
            	$_c = $Task->getCountDone();
            	if($_c > $Task->getCount()) {
            		$_c = $Task->getCount();
				}

                $message = str_replace('{count:'.$Task->getPocketItemId().'}', $_c.'/'.$Task->getCount(), $message);

                $pocket += $_c;
                $pocket_need += $Task->getCount();
            }

            $message = str_replace('{pocket:'.$PocketTask->id.'}', $pocket.'/'.$pocket_need, $message);
        }

        return $message;
    }

    protected function getDescriptionFromInventory()
    {
        $message = $this->description_data;
        foreach ($this->task as $PocketTask) {
            $pocket = 0;
            $pocket_need = 0;

            foreach ($PocketTask->items as $Task) {
				$_c = $Task->info->getCount();

            	/*if($Task->info->getItemType() == BaseTask::ITEM_TYPE_DROP) {
            		$_c = $Task->getCountDone();
				} else {
					$_c = $Task->info->getCount();
				}*/

                $count = $_c > $Task->getCount() ? $Task->getCount() : $_c;
                $message = str_replace('{count:'.$Task->getPocketItemId().'}', $count.'/'.$Task->getCount(), $message);

                $pocket += $count;
                $pocket_need += $Task->getCount();
            }

            $message = str_replace('{pocket:'.$PocketTask->id.'}', $pocket.'/'.$pocket_need, $message);
        }

        return $message;
    }

    /**
     * @return mixed
     */
    public function getCompleteConditionMessage()
    {
        return $this->complete_condition_message;
    }

    /**
     * @return mixed
     */
    public function getUserQuestId()
    {
        return $this->user_quest_id;
    }

    /**
     * @return mixed
     */
    public function getUserPartId()
    {
        return $this->user_part_id;
    }

    public function isStarted()
    {
        return $this->is_started;
    }

    /**
     * @param $pocket_id
     * @return bool|Pocket
     */
    public function getPocketTask($pocket_id)
    {
        foreach ($this->task as $Pocket) {
            if($Pocket->id != $pocket_id) {
                continue;
            }

            return $Pocket;
        }

        return false;
    }
}
