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
use components\Component\VarDumper;
use components\Helper\FileHelper;
use components\Helper\StringHelper;
use components\Helper\TimeHelper;
use \components\Component\Quests\QuestList as UserQuestObj;
use components\Component\Quests\object\Part as PartObj;
use components\Component\Quests\object\Quest as QuestObj;
use components\Component\Quests\object\Task as TaskObj;
use components\models\CraftProf;
use components\models\Inventory;
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


class QuestTest extends QuestManual
{
    public function init()
    {
        // TODO: Implement init() method.
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
				//VarDumper::dump($_user_quest_id);
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
}