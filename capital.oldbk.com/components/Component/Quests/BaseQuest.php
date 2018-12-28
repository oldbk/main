<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 02.06.2016
 */

namespace components\Component\Quests;

use components\Component\AbstractComponent;
use components\Component\Config;
use components\Component\Db\CapitalDb;
use components\Component\Quests\check\CheckerEmpty;
use components\Component\Quests\check\iChecker;
use components\Helper\FileHelper;
use components\Helper\StringHelper;
use components\Helper\TimeHelper;
use \components\Component\Quests\QuestList as UserQuestObj;
use components\Component\Quests\object\Part as PartObj;
use components\Component\Quests\object\Quest as QuestObj;
use components\Component\Quests\object\Task as TaskObj;
use components\models\CraftProf;
use components\models\Inventory;
use components\models\Ivents;
use components\models\NewDelo;
use components\models\quest\QuestCondition;
use components\models\quest\QuestPart;
use components\models\quest\QuestPocket;
use components\models\quest\QuestPocketItem;
use components\models\quest\QuestPocketItemInfo;
use components\models\quest\QuestValidatorItem;
use components\models\quest\QuestValidatorItemInfo;
use components\models\quest\UserQuest;
use components\models\quest\UserQuestInfoStart;
use components\models\quest\UserQuestLog;
use components\models\quest\UserQuestPart;
use components\models\quest\UserQuestPartItem;
use components\models\User;
use \components\models\quest\QuestList;
use components\models\UserBadge;
use components\models\UsersCraft;

abstract class BaseQuest extends AbstractComponent
{
    abstract protected function init();

    /** @var User */
    protected $user;

    /** @var \components\Component\Quests\QuestList */
    protected $quest;

    protected $user_quest_info = array();

    protected $user_active_dialogs = array();

    protected $user_progress = [];

    public function run()
    {

    }

    protected $user_quest_id_by_quest_ids = array();

    /**
     * @return array
     */
    public function getUserQuestIds()
    {
        return array_values($this->user_quest_id_by_quest_ids);
    }

	/**
	 * @return array
	 */
    public function getProgress()
	{
		return $this->user_progress;
	}

    /**
     * @param $quest_id
     * @return bool
     */
    public function haveQuest($quest_id)
    {
        return array_key_exists($quest_id, $this->user_quest_id_by_quest_ids);
    }

    public function getQuestIds()
    {
        return array_keys($this->user_quest_id_by_quest_ids);
    }

    /**
     * @return \components\Component\Quests\QuestList
     */
    public function getQuest()
    {
        return $this->getUserQuestObj();
    }

    /**
     * @return \components\Component\Quests\QuestList
     */
    public function getUserQuestObj()
    {
        if(!$this->quest) {
            $this->quest = new UserQuestObj();
        }

        return $this->quest;
    }

    /**
     * @return array
     */
    public function getDialogsIds()
    {
        return $this->user_active_dialogs;
    }

    /**
     * @param $quest_id
     * @param $part_id
     * @return bool
     */
    public function havePart($quest_id, $part_id)
    {
        $flag = false;
        foreach ($this->getUserQuestObj()->getItems() as $Quest) {
            if($Quest->id != $quest_id) {
                continue;
            }

            foreach ($Quest->part as $Part) {
                if($Part->id != $part_id) {
                    continue;
                }

                $flag = true;
                break 2;
            }
        }

        return $flag;
    }

    /**
     * @param $quest_id
     * @param $part_number
     * @return bool
     */
    public function havePartNumber($quest_id, $part_number)
    {
        $flag = false;
        foreach ($this->getUserQuestObj()->getItems() as $Quest) {
            if($Quest->id != $quest_id) {
                continue;
            }

            foreach ($Quest->part as $Part) {
                if($Part->part_number != $part_number) {
                    continue;
                }

                $flag = true;
                break 2;
            }
        }

        return $flag;
    }

    /**
     * @param $user_quest_id
     * @return null|array
     */
    protected function getUserQuestInfo($user_quest_id)
    {
        foreach ($this->user_quest_info as $info) {
            if($info['user_quest_id'] == $user_quest_id) {
                return $info;
            }
        }

        return null;
    }

    /**
     * @param $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    protected function clear()
    {
        $this->getUserQuestObj()->clear();
        $this->user_quest_id_by_quest_ids = array();
        $this->user_quest_info = array();
    }

    /**
     * @return self
     */
    public function get()
    {
        //$time_start = microtime(true);
        try {
            $this->clear();

            if($this->user === null && $this->app()->webUser->isGuest()) {
                throw new \Exception('Нет пользователя');
            }

            if($this->user === null && ($this->user = $this->app()->webUser->getUser()) === null) {
                throw new \Exception('Нет пользователя');
            }

            if($this->user->getId() === null) {
                throw new \Exception('Нет пользователя');
            }
			$isAdmin = (int)Config::admins($this->user->id);
			$UserQuest = UserQuest::from('user_quest as uq')
				->join('quest_list as ql', 'ql.id', '=', 'uq.quest_id')
				->whereRaw('uq.is_finished = 0 and uq.is_cancel = 0 and uq.is_end = 0 and ql.is_deleted = 0')
				->where('uq.user_id', '=', $this->user->getId())
				->when(!$isAdmin, function($query) {
					/** @var mixed $query */
					$query->where('ql.is_enabled', '=', 1);
				})
				->select(['uq.id', 'uq.quest_id', 'uq.dialog_id_save'])
				->get()->toArray();
            //Квесты пользователя, которые активные
            foreach ($UserQuest as $Quest) {
                $this->user_active_dialogs[] = $Quest['dialog_id_save'];
                $this->user_quest_id_by_quest_ids[$Quest['quest_id']] = $Quest['id'];
            }

            if(!$this->user_quest_id_by_quest_ids) {
                throw new \Exception(); //У пользователя отсутвуют квесты
            }

            $user_quest_ids = array_values($this->user_quest_id_by_quest_ids);
            $quest_ids = array_keys($this->user_quest_id_by_quest_ids);

            $this->user_quest_info = $this->getUserQuestStartInfo($user_quest_ids);

            $QuestList = $this->getFullQuestList($quest_ids);
            foreach ($QuestList as $quest_id => $QuestInfo) {
                if(!in_array($quest_id, $quest_ids)) {
                    continue;
                }

                $_user_quest_id = isset($this->user_quest_id_by_quest_ids[$quest_id]) ? $this->user_quest_id_by_quest_ids[$quest_id] : null;
                $this->getUserQuestObj()->addQuest($QuestInfo['quest']);
                foreach ($QuestInfo['parts'] as $part_id => $PartInfo) {
                    if($this->checkCondition($PartInfo['conditions'], $_user_quest_id) === true) {
                        $this->getUserQuestObj()->addPartArray($PartInfo['part']);

                        foreach ($PartInfo['pockets'] as $pocket_id => $PocketInfo) {
                            $this->getUserQuestObj()->addPocketArray($PocketInfo);
                        }
                    }
                }
            }
            $this->getUserQuestObj()->buildObject($this->user);

            $UserProgress = $this->getUserProgress($user_quest_ids);
            $this->user_progress = $UserProgress;
            foreach ($UserProgress as $user_quest_id => $QuestInfo) {
                foreach ($QuestInfo as $user_part_id => $PartInfo) {
                    $quest_id = $PartInfo['part']['quest_id'];
                    $part_id = $PartInfo['part']['quest_part_id'];

                    $this->getUserQuestObj()->addUserQuestId($quest_id, $user_quest_id);
                    $this->getUserQuestObj()->setUserPart($quest_id, $part_id, $PartInfo['part']);

                    foreach ($PartInfo['tasks'] as $user_task_id => $Task) {
                        if($this->getUserQuestObj()->addUserTask($Task) === false) {
                            $_data = [
								'is_deleted' => 1,
							];
							UserQuestPartItem::where('id', '=', $Task['id'])->update($_data);
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            $this->clear();
            FileHelper::writeException($ex, 'quest_get');
        }

        $this->init();

        //$time = microtime(true) - $time_start;
        //FileHelper::write('Quest time: '.$time, 'quest_get_'.$this->user->id);

        return $this;
    }

    protected function getUserProgress($user_quest_ids)
    {
        $user_part_list = array();

        //region get user active parts
		$UserParts = UserQuestPart::whereIn('user_quest_id', $user_quest_ids)
			->where('user_id', '=', $this->user->getId())
			->get()->toArray();
        foreach ($UserParts as $Part) {
            $user_quest_id = $Part['user_quest_id'];
            $user_part_list[$user_quest_id][$Part['id']] = array(
                'part' => $Part,
                'tasks' => array()
            );
        }
        unset($UserParts);
        //endregion

        //region get user active tasks
		$UserTasks = UserQuestPartItem::whereIn('user_quest_id', $user_quest_ids)
			->where('user_id', '=', $this->user->getId())
			->whereRaw('is_deleted = 0')
			->get()->toArray();
        foreach ($UserTasks as $Task) {
            $user_quest_id = $Task['user_quest_id'];
            $user_quest_part_id = $Task['user_quest_part_id'];
            $user_part_list[$user_quest_id][$user_quest_part_id]['tasks'][$Task['id']] = $Task;
        }
        unset($UserTasks);
        //endregion

        return $user_part_list;
    }

    protected function getUserQuestStartInfo($user_quest_ids)
    {
        $start_info = array();
        $from_cache_ids = array();
        //region get info from cache and delete user quest id
        if($cache = $this->app()->cache->get('user_quest_start_'.$this->user->id)) {
            foreach ($cache as $user_quest_id => $info) {
                $from_cache_ids[] = $user_quest_id;

                $user_part_list[$user_quest_id]['startInfo'] = $info;
                $start_info[$user_quest_id] = $info;
            }
        }
        //endregion
        $user_quest_ids = array_diff($user_quest_ids, $from_cache_ids);
        if(!$user_quest_ids) {
            return $start_info;
        }

        //region get start info
		$user_quest_info = UserQuestInfoStart::whereIn('user_quest_id', $user_quest_ids)->get()->toArray();
        foreach ($user_quest_info as $_info) {
            $user_part_list[$_info['user_quest_id']]['startInfo'] = $_info;
            $start_info[$_info['user_quest_id']] = $_info;
        }
        //endregion

        //save all info to cache
        $this->app()->cache->set('user_quest_start_'.$this->user->id, $start_info);

        return $start_info;
    }

    protected function getConditions($item_ids, $item_type)
    {
        $condition_list = array();
		$QuestConditionList = QuestCondition::whereIn('item_id', $item_ids)
			->where('item_type', '=', $item_type)
			->get()->toArray();
        foreach ($QuestConditionList as $Condition) {
            $item_id = $Condition['item_id'];

            $condition_list[$item_id][$Condition['condition_type']][$Condition['group']][$Condition['field']] = $Condition['value'];
        }
        unset($QuestConditionList);

        return $condition_list;
    }

    protected function getFullQuestList($quest_ids)
    {
        $quest_list = array();
        $part_list = array();
        $pocket_list = array();
        $pocket_item_list = array();
        $validator_list = array();
        $isAdmin = (int)Config::admins($this->user->id);

        $from_cache_ids = array();
        //only new quest get from DB, other get from cache
        foreach ($quest_ids as $key => $quest_id) {
            if($this->app()->cache->isExisting('quest_list_'.$quest_id)) {
                $from_cache_ids[] = $quest_id;

                $quest_list[$quest_id] = $this->app()->cache->get('quest_list_'.$quest_id);
            }
        }
        $quest_ids = array_diff($quest_ids, $from_cache_ids);
        if(!$quest_ids) {
            return $quest_list;
        }

		$QuestList = QuestList::whereIn('id', $quest_ids)
			->whereRaw('is_deleted = 0')
			->when(!$isAdmin, function($query) {
				/** @var mixed $query */
				$query->where('is_enabled', '=', 1);
			})
			->get()->toArray();
        foreach ($QuestList as $Quest) {
            $quest_list[$Quest['id']] = array(
                'quest' => $Quest,
                'conditions' => array(),
                'parts' => array(),
            );
        }
        unset($QuestList);
        $quest_ids = array_keys($quest_list);

        $QuestConditionList = $this->getConditions($quest_ids, QuestCondition::ITEM_TYPE_QUEST);
        foreach ($QuestConditionList as $quest_id => $Conditions) {

            $quest_list[$quest_id]['conditions'] = $Conditions;
        }

		$QuestPartList = QuestPart::whereIn('quest_id', $quest_ids)
			->whereRaw('is_deleted=0')
			->orderBy('part_number')
			->get()->toArray();
        foreach ($QuestPartList as $Part) {
            $part_list[$Part['id']] = array(
                'part' => $Part,
                'conditions' => array(),
                'pockets' => array(),
            );
        }
        unset($QuestPartList);
        $part_ids = array_keys($part_list);

        $QuestConditionList = $this->getConditions($part_ids, QuestCondition::ITEM_TYPE_PART);
        foreach ($QuestConditionList as $part_id => $Conditions) {

            $part_list[$part_id]['conditions'] = $Conditions;
        }

        //Награда за квест
        if($part_ids) {
			$TempPockets = QuestPocket::whereIn('item_id', $part_ids)->get()->toArray();
            foreach ($TempPockets as $Pocket) {
                $pocket_list[$Pocket['id']] = array(
                    'pocket' => $Pocket,
                    'items' => array(),
                );
            }
            unset($TempPockets);
        }
        $pocket_ids = array_keys($pocket_list);

        if($pocket_ids) {
			$TempItems = QuestPocketItem::whereIn('pocket_id', $pocket_ids)->get()->toArray();
            foreach ($TempItems as $_item) {
                $pocket_item_list[$_item['id']] = array(
                    'item' => $_item,
                    'info' => array(),
                    'validators' => array(),
                );
            }
            unset($TempItems);
        }
        $pocket_item_ids = array_keys($pocket_item_list);

        if($pocket_item_ids) {
			$TempInfo = QuestPocketItemInfo::whereIn('item_id', $pocket_item_ids)->get()->toArray();
            foreach ($TempInfo as $_info) {
                $pocket_item_list[$_info['item_id']]['info'][$_info['field']] = $_info['value'];
            }
            unset($TempInfo);
        }

        //region get validators and put to the pocket item
        if($pocket_item_ids) {
			$TempValidator = QuestValidatorItem::whereIn('parent_id', $pocket_item_ids)->get()->toArray();
            foreach ($TempValidator as $Validator) {
                $validator_list[$Validator['id']] = array(
                    'validator' => $Validator,
                    'info' => array(),
                );
            }
            unset($TempValidator);
        }
        $validator_ids = array_keys($validator_list);

        if($validator_ids) {
			$TempInfo = QuestValidatorItemInfo::whereIn('item_id', $validator_ids)->get()->toArray();
            foreach ($TempInfo as $_info) {
                $validator_list[$_info['item_id']]['info'][$_info['field']] = $_info['value'];
            }
            unset($TempInfo);
        }

        foreach ($validator_list as $validator_id => $_info) {
            $pocket_item_id = $_info['validator']['parent_id'];
            $pocket_item_list[$pocket_item_id]['validators'][$validator_id] = $_info;
        }
        //endregion

        //region put pocket_item to the pocket
        foreach ($pocket_item_list as $pocket_item_id => $_info) {
            $pocket_id = $_info['item']['pocket_id'];
            $pocket_list[$pocket_id]['items'][$pocket_item_id] = $_info;
        }
        //endregion

        //region put pocket to the part
        foreach ($pocket_list as $pocket_id => $_info) {
            $part_id = $_info['pocket']['item_id'];
            $part_list[$part_id]['pockets'][$pocket_id] = $_info;
        }
        //endregion

        //region put part to the quest
        foreach ($part_list as $part_id => $_info) {
            $quest_id = $_info['part']['quest_id'];
            $quest_list[$quest_id]['parts'][$part_id] = $_info;
        }
        //endregion

        foreach ($quest_list as $quest_id => $_info) {
            $this->app()->cache->set('quest_list_'.$quest_id, $_info, 300);
        }

        return $quest_list;
    }

    /**
     * @param $condition_list
     * @param null $user_quest_id
     * @return bool
     */
    protected function checkCondition($condition_list, $user_quest_id = null)
    {
        $level = $this->user->level;
        $align = $this->user->getGlobalAbility();
        $craft_levels = array();
        if($user_quest_id && ($info = $this->getUserQuestInfo($user_quest_id)) !== null) {
            $level = $info['level'];
            $align = $info['align'];
            $craft_levels = $info['craft_levels'] ? unserialize($info['craft_levels']) : array();
        }

        $date_condition = isset($condition_list['date']) ? $condition_list['date'] : array();
        foreach ($condition_list as $condition_type => $array) {
            foreach ($array as $group => $data) {
                switch ($condition_type) {
                    case 'level':
                        if($level < $data['min_level'] || $level > $data['max_level']) {
                            return false;
                        }
                        break;
                    case 'align':
                        $aligns = explode(',', $data['aligns']);
                        if(!in_array($align, $aligns)) {
                            return false;
                        }
                        break;
                    case 'quest':
						$isFinished = UserQuest::whereRaw('user_id = ? and quest_id = ? and is_finished = 1', [$this->user->getId(), $data['quest_id']])->count();
                        if(!$isFinished) {
                            return false;
                        }
                        break;
                    case 'item':
						$isItem = Inventory::whereRaw('owner = ? and prototype = ?', [$this->user->getId(), $data['item_prototype']])->count();
                        if(!$isItem) {
                            return false;
                        }
                        break;
                    case 'medal':
                        $find = false;
                        $badges = UserBadge::findByUserId($this->user->getId());
                        foreach ($badges as $badge) {
                            if($badge['rate_unique'] == $data['medal_key']) {
                                $find = true;
                                break;
                            }
                        }
                        if(!$find) {
                            return false;
                        }

                        break;
					case 'week':
						/** @var Ivents $Week */
						$Week = Ivents::where('stat', '=', 1)->first();
						if(!$Week) {
							return false;
						}

						if($Week->id != $data['week_id']) {
							return false;
						}

						break;
					case 'count':
						$builder = UserQuest::whereRaw('user_id = ? and quest_id = ? and (is_finished = 1 or is_end = 1)', [$this->user->getId(), $data['quest_id']]);
						if(isset($data['date_start'])) {
							$_datetime = new \DateTime($data['date']);
							$_datetime->setTime(0,0);

							$builder->where('created_at', '>=', $_datetime->getTimestamp());
						}
						if(isset($data['date_end'])) {
							$_datetime = new \DateTime($data['date_end']);
							$_datetime->setTime(23,59, 59);

							$builder->where('created_at', '<=', $_datetime->getTimestamp());
						}

						$count = $builder->count();
						if($count >= $data['count']) {
							return false;
						}

						break;
                    case 'prof':
                        if(empty($craft_levels)) {
                            $craft_levels = UsersCraft::getLevelsByUser($this->user->id);
                        }

                        /** @var CraftProf $Profession */
                        $Profession = CraftProf::find($data['profession_id'], ['name']);
                        if(!$Profession) {
                            return false;
                        }
						$Profession = $Profession->toArray();
                        $levelField = $Profession['name'].'level';

                        if(!isset($craft_levels[$levelField]) || $craft_levels[$levelField] < $data['level']) {
                            return false;
                        }
                        break;
					case 'gender':
						if($this->user->sex != $data['gender']) {
							return false;
						}
						break;
                }
            }
        }

        if($date_condition) {
            $date_flag = false;
            $current = new \DateTime();
            foreach ($date_condition as $group => $data) {
                $datestart = new \DateTime($data['date']);
                $datestart->setTime(0,0);

                $dateend = new \DateTime($data['date']);
                $dateend->setTime(23,59,59);

                if($datestart < $current && $dateend > $current) {
                    $date_flag = true;
                    break;
                }
            }
            if($date_flag === false) {
                return false;
            }
        }

        return true;
    }

    protected function getQuestById($quest_id)
    {
    	if(!$quest_id) {
    		return null;
		}

        $QuestList = $this->getFullQuestList(array($quest_id));

        return isset($QuestList[$quest_id]) ? $QuestList[$quest_id] : null;
    }

    /**
     * @param $id
     * @return bool
     */
    public function canGetByID($id)
    {
        try {
            $Quest = $this->getQuestById($id);
            if(!$Quest) {
                return false;
            }

            return $this->canGet($Quest);
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_canGetById');
        }

        return false;
    }

    /**
     * @param $Quest
     * @return bool
     */
    public function canGet($Quest)
    {
        if(!$this->user->getId()) {
            return false;
        }
        if(!isset($Quest['quest'])) {
            $Quest = $this->getQuestById($Quest['id']);
        }
        if(!$Quest) {
            return false;
        }
        $Conditions = $Quest['conditions'];
        $Parts = $Quest['parts'];
        $Quest = $Quest['quest'];

        try {
            if($Quest['min_level'] > $this->user->level || $Quest['max_level'] < $this->user->level) {
                return false;
            }
            if($Quest['is_enabled'] == 0 && Config::admins() == false) {
                return false;
            }

            if($this->checkCondition($Conditions) === false) {
                return false;
            }
            $find = false;
            foreach ($Parts as $PartInfo) {
                if($this->checkCondition($PartInfo['conditions']) === true) {
                    $find = true;
                    break;
                }
            }
            if(!$find) {
                throw new \Exception;
            }

            $DateTime = new \DateTime();
            switch ($Quest['quest_type']) {
                case QuestList::TYPE_DATERANGE:
                    if($Quest['started_at'] > $DateTime->getTimestamp() || $Quest['ended_at'] < $DateTime->getTimestamp()) {
                        return false;
                    }
                    break;
                case QuestList::TYPE_LIMITED:
                    if($Quest['limit_count'] > 0) {
						$count = UserQuest::whereRaw('user_id = ? and quest_id = ? and (is_finished = 1 or is_end = 1)',
							[$this->user->getId(), $Quest['id']])->count();
                        if($count >= $Quest['limit_count']) {
                            return false;
                        }
                    }
                    break;
                case QuestList::TYPE_INTERVAL:
                    $interval_sec = $Quest['limit_interval'] * 3600;

					/** @var UserQuest $UserQuest */
					$UserQuest = UserQuest::whereRaw('user_id = ? and quest_id = ?', [$this->user->getId(), $Quest['id']])
						->orderBy('id', 'desc')
						->first();
					if($UserQuest && ($UserQuest->ended_at == 0 || ($DateTime->getTimestamp() - $UserQuest->ended_at) < $interval_sec)) {
						return false;
					}
                    break;
                case QuestList::TYPE_DAILY:
                    $_current = new \DateTime();
                    $current_string = $_current->format('d.m.Y');
					$count = UserQuest::whereRaw('user_id = ? and is_cancel = 0 and quest_id = ?', [$this->user->getId(), $Quest['id']])
						->whereRaw('DATE_FORMAT(FROM_UNIXTIME(created_at), "%d.%m.%Y") = ?', [$current_string])
						->count();
                    if($count && !Config::admins($this->user->id)) {
                        return false;
                    }
                    break;
				case QuestList::TYPE_WEEKLY:
					$_day = date('w');

					$count = UserQuest::whereRaw('user_id = ? and quest_id = ? and is_cancel = 0', [$this->user->getId(), $Quest['id']])
						->where('created_at', '>=', strtotime('-'.$_day.' days'))
						->where('created_at', '<=', strtotime('+'.(6-$_day).' days'))
						->count();
					if($count && !Config::admins($this->user->id)) {
						return false;
					}
					break;
                default:
                    return false;
            }
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_canGet');
            return false;
        }

        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function addQuest($id)
    {
        if(!$this->user->getId()) {
            return false;
        }
        $Quest = $this->getQuestById($id);
        if(!$Quest) {
            return false;
        }
        $Parts = $Quest['parts'];
        $Quest = $Quest['quest'];

        $db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            $ValidParts = array();
            foreach ($Parts as $PartInfo) {
                if($this->checkCondition($PartInfo['conditions']) === true) {
                    $ValidParts[] = $PartInfo;
                }
            }

            if(!$ValidParts) {
                throw new \Exception('invalid part');
            }

            $_data = array(
                'user_id'       => $this->user->getId(),
                'quest_id'      => $id,
                'is_finished'   => 0,
                'is_cancel'     => 0,
                'is_end'        => 0,
                'created_at'    => time()
            );
			$user_quest_id = UserQuest::insertGetId($_data);
            if(!$user_quest_id) {
                throw new \Exception('can\'t insert quest');
            }

            $_data = array(
                'user_quest_id' => $user_quest_id,
                'level'         => $this->user->level,
                'align'         => $this->user->getGlobalAbility(),
                'quest_id'      => $Quest['id'],
                'craft_levels'  => serialize(UsersCraft::getLevelsByUser($this->user->id)),
            );
            if(!UserQuestInfoStart::insert($_data)) {
                throw new \Exception('can\'t insert UserQuestInfoStart');
            }

            $i = 0;
            foreach ($ValidParts as $PartInfo) {
                $Part = new PartObj(array(
                    'id'            => $PartInfo['part']['id'],
                    'quest_id'      => $PartInfo['part']['quest_id'],
                    'chat_start'    => $PartInfo['part']['chat_start'],
                ));
                $i++;
                $_data = array(
                    'user_quest_id' => $user_quest_id,
                    'quest_id'      => $id,
                    'quest_part_id' => $Part->id,
                    'user_id'       => $this->user->getId(),
                    'is_finished'   => 0,
                    'is_started'    => $i==1,
                    'started_at'    => $i==1?time():0,
                    'ended_at'      => 0,
                    'part_number'   => $i,
                );
				$user_part_id = UserQuestPart::insertGetId($_data);
                if(!$user_part_id) {
                    throw new \Exception('can\'t insert UserQuestPart');
                }

                if($_data['is_started']) {
                    $Part->sendStartMessage($this->user);
                }

                foreach ($PartInfo['pockets'] as $TaskPocket) {
                    foreach ($TaskPocket['items'] as $pocket_item_id => $TaskInfo) {
                        $Task = $TaskInfo['item'];
                        if($Task['pocket_item_type'] != QuestPocket::TYPE_PART_TASK) {
                            continue;
                        }

                        $_data = array(
                            'user_quest_id'         => $user_quest_id,
                            'user_quest_part_id'    => $user_part_id,
                            'quest_id'              => $Part->quest_id,
                            'quest_part_id'         => $Part->id,
                            'user_id'               => $this->user->getId(),
                            'item_id'               => $pocket_item_id,
                            'count'                 => isset($TaskInfo['info']['start_count']) ? (int)$TaskInfo['info']['start_count'] : 0,
                            'need_count'            => $Task['count'],
                            'ended_at'              => 0,
                            'is_finished'           => 0,
                        );

                        if(!UserQuestPartItem::insert($_data)) {
							throw new \Exception('can\'t insert UserQuestPartItem');
                        }
                    }
                }
            }

            $_data = array(
                'owner'                 => $this->user->getId(),
                'owner_login'           => $this->user->login,
                'owner_balans_do'       => $this->user->money,
                'owner_balans_posle'    => $this->user->money,
                'target_login'          => 'Квест',
                'type'                  => NewDelo::TYPE_QUEST_ADD,
                'add_info'              => $Quest['name'],
                'sdate'                 => time(),
            );
            if(!NewDelo::addNew($_data)) {
				throw new \Exception('can\'t insert NewDelo');
            }

            $db->commit();

            return true;
        } catch (\Exception $ex) {
            $db->rollBack();

            FileHelper::writeException($ex, 'quest_add', 'log');
        }

        return false;
    }

    /**
     * @param $Checker
     * @param $multiple
     * @return bool|TaskObj|TaskObj[]
     */
    public function isNeed($Checker, $multiple = false)
    {
        try {
            if(!($Checker instanceof iChecker)) {
                return false;
            }
            $Checker->setUser($this->user);

            $Items = $this->getUserQuestObj()->getTasksByChecker($Checker, $multiple);

			if(!$multiple && isset($Items[0])) {
            	return $Items[0];
			}

			return empty($Items) ? false : $Items;

        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_isNeed');
        }

        return false;
    }

	/**
	 * @param TaskObj[] $Items
	 * @return bool
	 */
    public function taskUpMultiple($Items)
	{
		$returned = false;

		if(!$this->user->getId()) {
			return $returned;
		}

		foreach ($Items as $Item) {
			$db = CapitalDb::connection();
			$db->beginTransaction();
			try {
				if(!$this->checkTask($Item)) {
					throw new \Exception('1');
				}

				$quest_id = $Item->getQuestId();
				$part_id = $Item->getItemId();

				$FinishedPocket = $this->checkPocketFinished($quest_id, $part_id, $Item->pocket_id);
				if($FinishedPocket && $FinishedPocket->dialog_finish_id) {
					$_data = [
						'custom_dialog_id' => (int)$FinishedPocket->dialog_finish_id
					];

					if(!UserQuest::whereRaw('id = ? and user_id = ?', [$Item->user_quest_id, $this->user->getId()])->update($_data)) {
						throw new \Exception('2');
					}
				}

				if($this->canFinishPart($quest_id, $part_id) !== false && $this->tryFinishPart($quest_id, $part_id) == false) {
					throw new \Exception('3');
				}

				if($this->canFinishQuest($quest_id) !== false && $this->tryFinishQuest($quest_id) == false) {
					throw new \Exception('4');
				}

				$db->commit();

			} catch (\Exception $ex) {
				$db->rollBack();
				FileHelper::writeException($ex, 'quest_taskUpMultiple');
			}
		}

		return true;
	}

	/**
	 * @param TaskObj $Item
	 * @return bool
	 */
	public function taskUp(TaskObj $Item)
	{
		return $this->taskUpMultiple([$Item]);
	}

    /**
     * @param TaskObj $Item
     * @return bool
     */
    protected function checkTask(TaskObj $Item)
    {
        try {
            $Item->addCountDone($Item->getUpCount());
            $Quest = $this->getUserQuestObj()->getQuest($Item->getQuestId());

            $Part = $this->getUserQuestObj()->getPart($Item->getQuestId(), $Item->getItemId());
            if($Part === false || $Quest === false) {
				throw new \Exception(sprintf('Can\'t find quest or part %s - %s. User: %s', $Item->getQuestId(), $Item->getItemId(), $this->user->getId()));
            }
            $isFinished = $Item->isFinished();

            if(!$Part->getUserPartId()) {
                $_data = [
					'user_quest_id' => $Part->getUserQuestId(),
					'quest_id'      => $Quest->id,
					'quest_part_id' => $Part->id,
					'user_id'       => $this->user->getId(),
					'is_finished'   => 0,
					'is_started'    => 1,
					'started_at'    => time(),
					'ended_at'      => 0,
					'part_number'   => 1,
				];
				$user_part_id = UserQuestPart::insertGetId($_data);
                if(!$user_part_id) {
					throw new \Exception(sprintf('Can\'t insert %s', implode(',', $_data)));
                }

                $Part->user_part_id = $user_part_id;
            }

            $_data = [
				'count'         => $Item->getCountDone(),
				'is_finished'   => (int)$isFinished,
				'ended_at'      => $isFinished ? time() : 0,
				'process'       => serialize($Item->getProcess())
			];
            if($Item->getUserTaskId() !== null) {
                if(!UserQuestPartItem::where('id', '=', $Item->getUserTaskId())->update($_data)) {
                    throw new \Exception(sprintf('Can\'t update %s - %s', implode(',', $_data), $Item->getUserTaskId()));
                }
            } else {
                $_data = array_merge($_data, [
					'user_quest_id'         => $Part->getUserQuestId(),
					'user_quest_part_id'    => $Part->getUserPartId(),
					'quest_id'              => $Part->quest_id,
					'quest_part_id'         => $Part->id,
					'user_id'               => $this->user->getId(),
					'item_id'               => $Item->getPocketItemId(),
					'need_count'            => $Item->getCount(),
					'count'            		=> $Item->getStartCount(),
				]);

				$Item->user_task_id = UserQuestPartItem::insertGetId($_data);
                if(!$Item->user_task_id) {
					throw new \Exception(sprintf('Can\'t insert %s', implode(',', $_data)));
                }
            }

            try {
                $_data = [
					'user_id'           => $this->user->getId(),
					'quest_id'          => $Part->quest_id,
					'part_id'           => $Part->id,
					'pocket_item_id'    => $Item->pocket_item_id,
					'user_quest_id'     => $Part->getUserQuestId(),
					'user_part_id'      => $Part->getUserPartId(),
					'user_task_id'      => $Item->getUserTaskId(),
					'check_count'       => $Item->getUpCount(),
					'created_at'        => time(),
				];
                UserQuestLog::insert($_data);
            } catch (\Exception $ex) {
                FileHelper::writeException($ex, 'quest_checkTask');
            }

            if($Item->isFinished() && $Part->isReadyToFinish($Item->getUserTaskId())) {
                $Part->sendCompleteMessage($this->user);
            }

            return true;
        } catch (\Exception $ex) {
            $Item->takeCountDone($Item->getUpCount());
			FileHelper::writeException($ex, 'quest_checkTask');
        }

        return false;
    }

	/**
	 * @param $quest_id
	 * @param $part_id
	 * @param $pocket_id
	 * @return bool|object\Pocket|null
	 */
	protected function checkPocketFinished($quest_id, $part_id, $pocket_id)
	{

		$isFinished = true;

		/** @var PartObj $Part */
		$Part = $this->getUserQuestObj()->getPart($quest_id, $part_id);
		if($Part === false) {
			return null;
		}

		foreach ($Part->task as $Pocket) {
			if($Pocket->id != $pocket_id) {
				continue;
			}

			foreach ($Pocket->items as $Item) {
				if(!$Item->isFinished()) {
					$isFinished = false;
					break;
				}
			}

			if($isFinished) {
				return $Pocket;
			}
		}

		return null;
	}

    /**
     * @param $quest_id
     * @param $part_id
     * @param $autocheck
     * @return bool
     */
    protected function canFinishPart($quest_id, $part_id, $autocheck = true)
    {
        /** @var PartObj $Part */
        $Part = $this->getUserQuestObj()->getPart($quest_id, $part_id);
        if($Part === false) {
            return false;
        }

        if($autocheck && !$Part->is_auto_finish) {
            return false;
        }

        if($Part->isReadyToFinish()) {
            return true;
        }

        $Checker = new CheckerEmpty();
        $Checker->setUser($this->user);
        try {
            foreach ($Part->task as $Pocket) {
                foreach ($Pocket->items as $Item) {
                    if($Item->isFinished()) {
                        continue;
                    }

                    if($Checker->getCheckerType() != $Item->info->getItemType()) {
                        throw new \Exception();
                    }

                    $Checker->count = $Item->getCount();
                    if($Item->check($Checker) == false) {
                        throw new \Exception('Fail to check');
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_canFinishPart');
        }

        return false;
    }

    /**
     * @param $quest_id
     * @param $part_id
     * @return bool
     */
    protected function tryFinishPart($quest_id, $part_id)
    {
        $Part = $this->getUserQuestObj()->getPart($quest_id, $part_id);
        if($Part === false) {
            return false;
        }

        if($Part->is_finished || !$Part->is_started) {
            return true;
        }

        try {
            $Checker = new CheckerEmpty();
            $Checker->setUser($this->user);
            foreach ($Part->task as $Pocket) {
                foreach ($Pocket->items as $Item) {
                    if($Checker->getCheckerType() != $Item->info->getItemType() || $Item->isFinished()) {
                        continue;
                    }

                    $Checker->count = $Item->getCount();
                    if($this->checkTask($Item) == false) {
                        throw new \Exception('Check task fail');
                    }
                }
            }

            if($this->finishPart($Part) === false) {
                throw new \Exception("Can't finish part");
            }

            return true;
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_tryFinishPart');
        }

        return false;
    }

    /**
     * @param PartObj $Part
     * @return bool
     */
    protected function finishPart(PartObj $Part)
    {
        if($Part->is_finished || !$Part->is_started) {
            return true;
        }

        try {
            $_data = array(
                'ready_to_finish' => 1,
            );

            $_data = array_merge($_data, array(
                'is_finished'       => 1,
                'is_started'        => 0,
                'ended_at'          => time(),
            ));
            if(!UserQuestPart::where('id', '=', $Part->user_part_id)->update($_data)) {
                throw new \Exception('1');
            }

            if($Part->giveReward($this->user) === false) {
                throw new \Exception('2');
            }

            if($Part->take($this->user) === false) {
                throw new \Exception('3');
            }

            $Part->is_finished = true;

            $Part->sendFinishedMessage($this->user);
            //$Part->sendCompleteMessage($this->user);


			$Quest = $this->getUserQuestObj()->getQuest($Part->quest_id);
			$NextPart = $Quest->getPartByNumber($Part->part_number + 1);
			if(!$NextPart || !$NextPart->is_auto_start) {
				return true;
			}

			if($this->startPart($NextPart) == false) {
				throw new \Exception('4');
			}

            return true;
        } catch (\Exception $ex) {
            $Part->is_finished = false;
            FileHelper::writeException($ex, 'quest_finishPart');
        }

        return false;
    }

    protected function startPart(PartObj $Part)
    {
        try {
            $_data = array(
                'is_started' => 1,
                'started_at' => time(),
            );
			UserQuestPart::where('id', '=', $Part->user_part_id)->update($_data);

			$Part->is_started  = 1;
            $Part->sendStartMessage($this->user);

            return true;
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_startPart');
        }

        return false;
    }

    /**
     * @param $quest_id
     * @param $autocheck
     * @return bool
     */
    protected function canFinishQuest($quest_id, $autocheck = true)
    {
        $Quest = $this->getUserQuestObj()->getQuest($quest_id);
        if($Quest->isFinished()) {
            return true;
        }

        try {
            foreach ($Quest->part as $Part) {
                if($this->canFinishPart($Quest->id, $Part->id, $autocheck) == false) {
                    throw new \Exception();
                }
            }

            return true;
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_canFinishQuest');
        }

        return false;
    }

    /**
     * @param $quest_id
     * @return bool
     */
    protected function tryFinishQuest($quest_id)
    {
        $Quest = $this->getUserQuestObj()->getQuest($quest_id);

        try {
            foreach ($Quest->part as $Part) {
                if($Part->is_finished) {
                    continue;
                }

                if($this->canFinishPart($Quest->id, $Part->id, false) == false || $this->tryFinishPart($Quest->id, $Part->id) == false) {
                    throw new \Exception();
                }
            }

            if($this->finishQuest($Quest) === false) {
                throw new \Exception();
            }

            return true;
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_tryFinishQuest');
        }

        return false;
    }

    /**
     * @param QuestObj $Quest
     * @return bool
     * @throws \Exception
     */
    protected function finishQuest(QuestObj $Quest)
    {
        if(!$this->user->getId()) {
            return false;
        }

        $_data = array(
            'is_finished'   => 1,
            'ended_at'      => time(),
        );
        if(!UserQuest::where('id', '=', $Quest->user_quest_id)->update($_data)) {
			throw new \Exception;
		}

        $_data = array(
            'owner'                 => $this->user->getId(),
            'owner_login'           => $this->user->login,
            'owner_balans_do'       => $this->user->money,
            'owner_balans_posle'    => $this->user->money,
            'target_login'          => 'Квест',
            'type'                  => NewDelo::TYPE_QUEST_FINISH,
            'add_info'              => $Quest->name,
            'sdate'                 => time(),
        );
        if(!NewDelo::addNew($_data)) {
            throw new \Exception;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getDescriptions()
    {
        $response = array();

        try {
            foreach ($this->getUserQuestObj()->getItems() as $Quest) {
                foreach ($Quest->part as $Part) {
                    if(!$Part->isStarted()) {
                        continue;
                    }

					$description = $Part->getDescription();
					$description = StringHelper::prepareGender($description, $this->user->sex);
                    switch ($Quest->quest_type) {
                        case QuestList::TYPE_DAILY:
                            $title = '';
                            if($Part->img) {
                                $title .= sprintf('<img src="%s" width="" title="%s" height="25"><br>', $Part->img, $Part->name);
                            }
                            $title .= sprintf('<strong>%s</strong>', $Part->name);

                            $starttime = new \DateTime();
                            $endtime = new \DateTime();
                            $endtime->setTime(23,59,59);
                            $response[] = array(
                                0 => $title,
                                1 => $description,
                                2 => sprintf('<div style="text-align: left;">Осталось: <strong>%s</strong></div>', TimeHelper::prettyTime($starttime->getTimestamp(), $endtime->getTimestamp()))
                                    . ($Quest->isCanceled() ?
                                        '<br>'.sprintf('<div style="text-align: left;font-weight: bold;"><a href="/action/quest/cancel?id=%d">Отменить</a></div>', $Quest->user_quest_id)
                                        : '')
                            );
                            break;
                        default:
                            $title = '';
                            if($Part->img) {
                                $title .= sprintf('<img src="%s" width="" title="%s" height="25"><br>', $Part->img, $Part->name);
                            }
                            $title .= sprintf('<strong>%s</strong>', $Part->name);

                            $response[] = array(
                                0 => $title,
                                1 => $description,
                                2 => $Quest->isCanceled()
                                    ? sprintf('<div style="text-align: left;font-weight: bold;"><a href="/action/quest/cancel?id=%d">Отменить</a></div>', $Quest->user_quest_id)
                                    : '',
                            );
                            break;
                    }
                }
            }
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_getDescriptions');
        }

        return $response;
    }

    /**
     * @return array
     */
    public function getDescriptionsInfo()
    {
        $response = array();

        try {
            foreach ($this->getUserQuestObj()->getItems() as $Quest) {
                foreach ($Quest->part as $Part) {
                    if(!$Part->isStarted()) {
                        continue;
                    }

					/** @var UserQuest $UserQuest */
					$UserQuest = UserQuest::find($Quest->user_quest_id);

                    $description = $Part->getDescription();
                    $description = StringHelper::prepareGender($description, $this->user->sex);
                    switch ($Quest->quest_type) {
                        case QuestList::TYPE_DAILY:
                            $title = '';
                            if($Part->img) {
                                $title .= sprintf('<img src="%s" width="" title="%s" height="25"><br>', $Part->img, $Part->name);
                            }
                            $title .= sprintf('<strong>%s</strong>', $Part->name);

                            $starttime = new \DateTime();
                            $endtime = new \DateTime();
                            $endtime->setTime(23,59,59);
                            $response[] = array(
                                0 => $title,
                                1 => $description.'<br>',
                                2 => sprintf('Осталось: %s', TimeHelper::prettyTime($starttime->getTimestamp(), $endtime->getTimestamp())),
								3 => sprintf('Квест: %d. Часть: %d. User квест: %d. Взят: %s', $Quest->id, $Part->id, $Quest->user_quest_id, date('d.m.Y H:i', $UserQuest->created_at))
                            );
                            break;
                        default:
                            $title = '';
                            if($Part->img) {
                                $title .= sprintf('<img src="%s" width="" title="%s" height="25"><br>', $Part->img, $Part->name);
                            }
                            $title .= sprintf('<strong>%s</strong>', $Part->name);

                            $response[] = array(
                                0 => $title,
                                1 => $description.'<br>',
                                2 => '',
								3 => sprintf('Квест: %d. Часть: %d. User квест: %d. Взят: %s', $Quest->id, $Part->id, $Quest->user_quest_id, date('d.m.Y H:i', $UserQuest->created_at))
                            );
                            break;
                    }
                }
            }
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_getDescriptions');
        }

        return $response;
    }

    public function cancelUserQuest($user_quest_id)
    {
        $db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            foreach ($this->getUserQuestObj()->getItems() as $Quest) {
                if($Quest->user_quest_id != $user_quest_id) {
                    continue;
                }

                if(!$Quest->isCanceled()) {
                    throw new \Exception();
                }

                foreach ($Quest->part as $Part) {
					$Part->take($this->user);
                }

                $_data = array(
                    'is_finished'   => 0,
                    'is_end'        => 0,
                    'is_cancel'     => 1,
                    'ended_at'      => time()
                );
                if(!UserQuest::where('id', '=', $user_quest_id)->update($_data)) {
					throw new \Exception;
				}
                $Quest->cancel = true;
            }

            $db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();
            FileHelper::writeException($ex, 'quest_cancel');

            return false;
        }

        return true;
    }
}
