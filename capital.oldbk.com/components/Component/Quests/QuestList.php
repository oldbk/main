<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.06.2016
 */

namespace components\Component\Quests;

use components\Component\Quests\check\iChecker;
use components\Component\Quests\object\Part as PartObj;
use components\Component\Quests\object\Pocket as PocketObj;
use components\Component\Quests\object\Quest as QuestObj;
use components\Component\Quests\object\Reward as RewardObj;
use components\Component\Quests\object\Take as TakeObj;
use components\Component\Quests\object\Task as TaskObj;
use components\Component\Quests\pocket\itemInfo\BaseInfo;
use components\Component\Quests\pocket\questTask\BaseTask;
use components\Component\Quests\validator\BaseValidator;
use components\Component\VarDumper;
use components\Helper\FileHelper;
use components\models\quest\QuestPocket;

class QuestList
{
    private $is_build = false;

    protected $items_array = array();
    /** @var QuestObj[] */
    protected $items = array();

    public function addFromCache($Quest)
    {
        foreach ($Quest as $quest_id => $_quest) {
            $this->items_array[$quest_id] = $_quest;
        }
        
        return $this;
    }

    public function clear()
    {
        unset($this->items_array);
        $this->items_array = array();

        unset($this->items);
        $this->items = array();
    }

    public function addQuest($quest)
    {
        $this->items_array[$quest['id']] = array(
            'info' => array(
                'id'            => (int)$quest['id'],
                'quest_type'    => $quest['quest_type'],
                'name'          => $quest['name'],
                'is_canceled'   => $quest['is_canceled'],
            ),
            'part' => array()
        );
    }

    public function addUserQuestId($quest_id, $user_quest_id)
    {
        if(!isset($this->items[$quest_id])) {
            return;
        }

        $Quest = $this->items[$quest_id];
        $Quest->user_quest_id = $user_quest_id;

        foreach ($Quest->part as $Part) {
            $Part->user_quest_id = $user_quest_id;
        }
    }

    public function addUserPartId($quest_id, $quest_part_id, $user_part_id)
    {
        if(!isset($this->items[$quest_id])) {
            return;
        }

        $Quest = $this->items[$quest_id];
        foreach ($Quest->part as $part_id => $Part) {
            if($part_id != $quest_part_id) {
                continue;
            }

            $Part->user_part_id = $user_part_id;
            break;
        }
    }

    public function setUserPart($quest_id, $quest_part_id, $part)
    {
        if(!isset($this->items[$quest_id])) {
            return;
        }

        $Quest = $this->items[$quest_id];
        foreach ($Quest->part as $part_id => $Part) {
            if($part_id != $quest_part_id) {
                continue;
            }

            $Part->user_part_id = $part['id'];
            $Part->is_started   = $part['is_started'];
            $Part->is_finished  = $part['is_finished'];
            break;
        }
    }

    public function addPartArray($part)
    {
        if(!isset($this->items_array[$part['quest_id']])) {
            return $this;
        }

        $this->items_array[$part['quest_id']]['part'][$part['id']] = array(
            'info' => array(
                'id'                            => (int)$part['id'],
                'quest_id'                      => (int)$part['quest_id'],
                'name'                          => $part['name'],
                'img'                           => $part['img'],
                'description_type'              => $part['description_type'],
                'description_data'              => $part['description_data'],
                'chat_start'                    => $part['chat_start'],
                'chat_end'                      => $part['chat_end'],
                'is_auto_finish'                => (int)$part['is_auto_finish'],
                'is_auto_start'                	=> isset($part['is_auto_start']) ? (int)$part['is_auto_start'] : 0,
                'part_number'                   => (int)$part['part_number'],
                'is_finished'                   => isset($part['is_finished']) ? $part['is_finished'] : false,
                'is_started'                    => $part['is_started'],
                'complete_condition_message'    => $part['complete_condition_message'],
            ),
            'task' => array(),
            'reward' => array()
        );

        return $this;
    }

    private $_items_pocket_ids = array();
    public function addPocketArray($data)
    {
        $pocket = $data['pocket'];
        $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : array();

        if(!isset($this->items_array[$pocket['global_parent_id']]['part'][$pocket['item_id']])) {
            return $this;
        }

        $temp = array(
            'id'        		=> $pocket['id'],
            'quest_id'  		=> $pocket['global_parent_id'],
            'item_id'   		=> $pocket['item_id'],
            'item_type' 		=> $pocket['item_type'],
            'condition' 		=> $pocket['condition'],
            'dialog_finish_id' 	=> isset($pocket['dialog_finish_id']) ? $pocket['dialog_finish_id'] : null,
            'items' => array(),
        );

        foreach ($items as $pocket_item_id => $item) {
            $this->_items_pocket_ids[$item['item_id']] = $pocket['id'];

            $temp['items'][$pocket_item_id] = array(
                'item' => array(
                    'pocket_id'         => (int)$pocket['id'],
                    'item_type'         => $item['item']['item_type'],
                    'pocket_item_id'    => (int)$pocket_item_id,
                    'item_id'           => (int)$pocket['item_id'],
                    'quest_id'          => (int)$pocket['global_parent_id'],
                    'count'             => (int)$item['item']['count'],
                    'can_be_multiple'   => isset($item['info']['can_be_multiple']) ? (int)$item['info']['can_be_multiple'] : 0,
                    'start_count'       => isset($item['info']['start_count']) ? (int)$item['info']['start_count'] : 0,
                ),
                'info' => $item['info'],
                'validators' => isset($item['validators']) ? $item['validators'] : array(),
            );
        }

        switch ($pocket['item_type']) {
            case QuestPocket::TYPE_PART_REWARD:
                $this->items_array[$pocket['global_parent_id']]['part'][$pocket['item_id']]['reward'][] = $temp;
                break;
            case QuestPocket::TYPE_PART_TASK:
                $this->items_array[$pocket['global_parent_id']]['part'][$pocket['item_id']]['task'][] = $temp;
                break;
            case QuestPocket::TYPE_PART_TAKE:
                $this->items_array[$pocket['global_parent_id']]['part'][$pocket['item_id']]['take'][] = $temp;
                break;
        }

        return $this;
    }

    public function addUserTask($Task)
    {
        if($this->is_build === false) {
            throw new \Exception('QuestList was not built');
        }

        if(!isset($this->items[$Task['quest_id']])) {
            throw new \Exception(sprintf('QuestList couldn\'t find Quest. Quest: %d. User: %d', $Task['quest_id'], $Task['user_id']));
        }
        $Quest = $this->items[$Task['quest_id']];

        if(!isset($Quest->part[$Task['quest_part_id']])) {
        	FileHelper::writeArray([
        		'part' => $Quest->part,
				'task' => $Task
			], $Task['user_id'], 'log');
            throw new \Exception(sprintf('QuestList couldn\'t find Part. Quest: %d. User: %d. Part: %d', $Task['quest_id'], $Task['user_id'], $Task['quest_part_id']));
        }
        $Part = $Quest->part[$Task['quest_part_id']];

        //VarDumper::d($Task);
        $find = false;
        foreach ($Part->task as $TaskPocket) {
            foreach ($TaskPocket->items as $TaskObj) {
                if($TaskObj->getPocketItemId() != $Task['item_id']) {
                    continue;
                }
                
                $TaskObj->count_done    = (int)$Task['count'];
                $TaskObj->user_quest_id = (int)$Task['user_quest_id'];
                $TaskObj->user_part_id  = (int)$Task['user_quest_part_id'];
                $TaskObj->user_task_id  = (int)$Task['id'];
                $TaskObj->is_finished   = (int)$Task['is_finished'];
                $TaskObj->process       = empty($Task['process']) ? array() : unserialize($Task['process']);

                $find = true;
                break 2;   
            }
        }
        
        return $find;
    }

    public function getItemsArray()
    {
        return $this->items_array;
    }

    public function getItems()
    {
        return $this->items;
    }

	/**
	 * @param iChecker $Checker
	 * @param bool $multiple
	 * @return TaskObj[]
	 */
    public function getTasksByChecker($Checker, $multiple = false)
	{
		$items = [];

		foreach ($this->getItems() as $quest_id => $Quest) {
			foreach ($Quest->part as $Part) {
				if(!$Part->isStarted()) {
					continue;
				}
				foreach ($Part->task as $Pocket) {
					/** @var TaskObj $Item */
					foreach ($Pocket->items as $Item) {

						if($Item->getCountDone() >= $Item->getCount()) {
							continue;
						}
						if($Checker->getCheckerType() != $Item->getItemType()) {
							continue;
						}
						if(($multiple && !$Item->canBeMultiple() && !empty($items))) {
							continue;
						}

						if($Item->check($Checker)) {
							$items[] = $Item;
							if(!$multiple) {
								return $items;
							}
						}
					}
				}
			}
		}

		return $items;
	}
    
    public function getForCache()
    {
        $temp = array();
        foreach ($this->items_array as $quest_id => $quest) {
            $temp[$quest_id] = $quest;
        }

        return $temp;
    }

    public function buildObject($user = null)
    {
        foreach ($this->items_array as $quest_id => $_quest) {
            if(!isset($_quest['info'])) {
                continue;
            }
            $Quest = new QuestObj($_quest['info']);

            foreach ($_quest['part'] as $part_id => $_part) {
                if(!isset($_part['info'])) {
                    continue;
                }
                $Part = new PartObj($_part['info']);

                if(isset($_part['task'])) {
                    foreach ($_part['task'] as $task) {
                        $Pocket = new PocketObj(array(
                            'condition' 		=> $task['condition'],
                            'item_type' 		=> $task['item_type'],
                            'id'        		=> isset($task['id']) ? $task['id'] : null,
                            'quest_id'  		=> isset($task['quest_id']) ? $task['quest_id'] : null,
                            'item_id'   		=> isset($task['item_id']) ? $task['item_id'] : null,
							'dialog_finish_id' 	=> isset($task['dialog_finish_id']) ? $task['dialog_finish_id'] : null,
                        ));
                        foreach ($task['items'] as $_item) {
                            $Task = new TaskObj($_item['item']);

                            $Task->info = BaseTask::getQuestTask($Task->getItemType());
                            $Task->info->populate(isset($_item['info']) ? $_item['info'] : array());
                            $Task->info->setUser($user);

                            if($_item['validators']) {
                                foreach ($_item['validators'] as $_validator) {
                                    $Validator = BaseValidator::getValidator($_validator['validator']['item_type']);
                                    $Validator->populate(isset($_validator['info']) && is_array($_validator['info']) ? $_validator['info'] : array());

                                    $Task->addValidator($Validator);
                                }
                            }

                            $Pocket->addItem($Task);
                        }
                        $Part->addTask($Pocket);
                    }
                }

                if(isset($_part['reward'])) {
                    foreach ($_part['reward'] as $reward) {
                        $Pocket = new PocketObj(array(
                            'condition' => $reward['condition'],
                            'item_type' => $reward['item_type'],
                            'id'        => isset($reward['id']) ? $reward['id'] : null,
                            'quest_id'  => isset($reward['quest_id']) ? $reward['quest_id'] : null,
                            'item_id'   => isset($reward['item_id']) ? $reward['item_id'] : null,
                        ));
                        foreach ($reward['items'] as $_item) {
                            $Reward = new RewardObj($_item['item']);

                            $Reward->info = BaseInfo::getItemInfo($Reward->getItemType());
                            $Reward->info->populate(isset($_item['info']) ? $_item['info'] : array());

                            if(isset($_item['validators']) && $_item['validators']) {
                                foreach ($_item['validators'] as $_validator) {
                                    $Validator = BaseValidator::getValidator($_validator['validator']['item_type']);
                                    $Validator->populate(isset($_validator['info']) && is_array($_validator['info']) ? $_validator['info'] : array());

                                    $Reward->addValidator($Validator);
                                }
                            }

                            $Pocket->addItem($Reward);
                        }
                        $Part->addReward($Pocket);
                    }
                }

                if(isset($_part['take'])) {
                    foreach ($_part['take'] as $take) {
                        $Pocket = new PocketObj(array(
                            'condition' => $take['condition'],
                            'item_type' => $take['item_type'],
                            'id'        => isset($take['id']) ? $take['id'] : null,
                            'quest_id'  => isset($take['quest_id']) ? $take['quest_id'] : null,
                            'item_id'   => isset($take['item_id']) ? $take['item_id'] : null,
                        ));
                        foreach ($take['items'] as $_item) {
                            $Take = new TakeObj($_item['item']);

                            $Take->info = BaseInfo::getItemInfo($Take->getItemType());
                            $Take->info->populate(isset($_item['info']) ? $_item['info'] : array());

                            $Pocket->addItem($Take);
                        }
                        $Part->addTake($Pocket);
                    }
                }

                $Quest->addPart($Part);
            }

            $this->items[$Quest->id] = $Quest;
        }

        $this->is_build = true;
    }

    /**
     * @param iChecker $Checker
     * @return bool|RewardObj|TaskObj
     * @deprecated
     */
    public function isNeed($Checker)
    {
        foreach ($this->items as $quest_id => $Quest) {
            foreach ($Quest->part as $Part) {
                if(!$Part->isStarted()) {
                    continue;
                }
                foreach ($Part->task as $Pocket) {
                    foreach ($Pocket->items as $Item) {
                        if($Checker->getCheckerType() != $Item->getItemType() || $Item->getCountDone() >= $Item->getCount()) {
                            continue;
                        }

                        if($Item->check($Checker)) {
                            return $Item;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param iChecker $Checker
     * @return bool|RewardObj|TaskObj
     */
    public function isNeedWithoutCount($Checker)
    {
        foreach ($this->items as $quest_id => $Quest) {
            foreach ($Quest->part as $Part) {
                if(!$Part->isStarted()) {
                    continue;
                }
                foreach ($Part->task as $Pocket) {
                    foreach ($Pocket->items as $Item) {
                        if($Checker->getCheckerType() != $Item->getItemType()) {
                            continue;
                        }

                        if($Item->check($Checker)) {
                            return $Item;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return bool|QuestObj
     */
    public function getQuest($id)
    {
        return isset($this->items[$id]) ? $this->items[$id] : false;
    }

    /**
     * @param $quest_id
     * @param $part_id
     * @return bool|PartObj
     */
    public function getPart($quest_id, $part_id)
    {
        if(($Quest = $this->getQuest($quest_id)) === false) {
            return false;
        }

        return isset($Quest->part[$part_id]) ? $Quest->part[$part_id] : false;
    }
}