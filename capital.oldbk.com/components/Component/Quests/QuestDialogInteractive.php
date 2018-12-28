<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Component\Quests;


use components\Component\Config;
use components\Component\VarDumper;
use components\Helper\LocationHelper;
use \components\models\quest\QuestDialog as Dialog;
use components\models\quest\UserQuest;

class QuestDialogInteractive extends QuestDialogNew
{
	protected $end_dialogs = true;

	public function getMainDialog()
	{
		$this->getQuestObj();

		$response_temp      = array();
		$response           = array();
		$save_quest_dialogs = array();
		$allow_dialog_ids   = array();
		$sorting            = array();

		if($this->save_dialogs && $Temp = $this->getSaveDialogs()) {
			foreach ($Temp as $Dialog) {
				$response_temp[] = array(
					'dialog'    => $Dialog['id'],
					'title'     => $Dialog['quest_name'],
					'bot_id'	=> $Dialog['bot_id'],
					'quest_id'	=> $Dialog['global_parent_id'],
					'state'		=> 'save',
				);
				$sorting[count($response_temp) - 1] = $Dialog['quest_name'];

				$allow_dialog_ids[] = $Dialog['id'];

				$save_quest_dialogs[] = $Dialog['global_parent_id'];
			}
		}

		//ѕолучаем диалоги дл€ завершени€ квеста
		if($this->end_dialogs && $Temp = $this->getEndDialogs()) {
			foreach ($Temp as $Dialog) {
				if(in_array($Dialog['id'], $allow_dialog_ids) || in_array($Dialog['global_parent_id'], $save_quest_dialogs)) {
					continue;
				}

				$response_temp[] = array(
					'dialog'    => $Dialog['id'],
					'title'     => $Dialog['quest_name'],
					'bot_id'	=> $Dialog['bot_id'],
					'quest_id'	=> $Dialog['global_parent_id'],
					'state'		=> 'end',
				);
				$sorting[count($response_temp) - 1] = $Dialog['quest_name'];

				$allow_dialog_ids[] = $Dialog['id'];

				$save_quest_dialogs[] = $Dialog['global_parent_id'];
			}
		}

		//ѕолучаем новые диалоги дл€ старта квеста
		if($this->start_dialogs && $Temp = $this->getStartDialogs()) {
			foreach ($Temp as $Dialog) {
				if($this->getQuestObj()->canGetByID($Dialog['global_parent_id']) === false) {
					continue;
				}


				if(in_array($Dialog['id'], $allow_dialog_ids) || in_array($Dialog['global_parent_id'], $save_quest_dialogs)) {
					continue;
				}

				$response_temp[] = array(
					'dialog'    => $Dialog['id'],
					'title'     => $Dialog['quest_name'],
					'bot_id'	=> $Dialog['bot_id'],
					'quest_id'	=> $Dialog['global_parent_id'],
					'state'		=> 'start',
				);
				$sorting[count($response_temp) - 1] = $Dialog['quest_name'];

				$allow_dialog_ids[] = $Dialog['id'];
			}
		}
		asort($sorting);
		foreach ($sorting as $key => $value) {
			$response[] = $response_temp[$key];
		}

		$_SESSION['allow_dialog_ids'][$this->bot_id] = $allow_dialog_ids;

		return $response;
	}

	protected function getStartDialogs()
	{
		$isAdmin = (int)Config::admins();
		$user = $this->app->webUser;
		$quest_have_ids = $this->getQuestObj()->getQuestIds();

		$builder = Dialog::from('quest_dialog as qd')
			->join('quest_list as ql', 'ql.id', '=', 'qd.global_parent_id')
			->leftJoin('user_dialog as ud', function($join) use ($user) {
				$join->on('ud.quest_id', '=', 'qd.global_parent_id')
					->on('ud.dialog_id', '=', 'qd.id')
					->where('ud.user_id', '=', $user->getId());
			})
			->where('qd.bot_id', '=', $this->bot_id)
			->where('qd.item_type', '=', Dialog::TYPE_QUEST)
			->where(function($query) use ($user) {
				$query->where('qd.location', '=', LocationHelper::ROOM_ALL)
					->orWhere('qd.location', '=', $user->getRoom());
			})
			->whereRaw('(ud.state is null or ud.state = 1) and ql.is_deleted = 0 and qd.order_position = 1')
			->when($quest_have_ids, function($query) use ($quest_have_ids) {
				$query->whereNotIn('qd.global_parent_id', $quest_have_ids);
			})
			->when(!$isAdmin, function($query) {
				$query->where('ql.is_enabled', '=', 1);
			})
			->select(['qd.global_parent_id', 'qd.id', 'ql.name as quest_name', 'qd.bot_id']);

		$result = $builder->get()->toArray();
		return $result;
	}

	protected function getSaveDialogs()
	{
		$isAdmin = (int)Config::admins();
		$user_quest_have_ids = $this->getQuestObj()->getUserQuestIds();
		if(!$user_quest_have_ids) {
			return array();
		}
		$user = $this->app->webUser;

		$builder = UserQuest::from('user_quest as uq')
			->join('quest_list as ql', 'ql.id', '=', 'uq.quest_id')
			->join('quest_dialog as qd', 'qd.id', '=', 'uq.dialog_id_save')
			->leftJoin('user_dialog as ud', function($join) use ($user) {
				$join->on('ud.quest_id', '=', 'qd.global_parent_id')
					->on('ud.dialog_id', '=', 'qd.id')
					->where('ud.user_id', '=', $user->getId());
			})
			->where(function($query) use ($user) {
				$query->where('qd.location', '=', LocationHelper::ROOM_ALL)
					->orWhere('qd.location', '=', $user->getRoom());
			})
			->whereIn('uq.id', $user_quest_have_ids)
			->where('qd.bot_id', '=', $this->bot_id)
			->whereRaw('(ud.state is null or ud.state = 1) and ql.is_deleted = 0 and uq.dialog_id_save > 0')
			->when(!$isAdmin, function($query) {
				$query->where('ql.is_enabled', '=', 1);
			})
			->select(['uq.quest_id as global_parent_id', 'uq.dialog_id_save as id', 'ql.name as quest_name', 'qd.bot_id']);

		$result = $builder->get()->toArray();
		return $result;
	}

	protected function getEndDialogs()
	{
		$isAdmin = (int)Config::admins();
		$user_quest_have_ids = $this->getQuestObj()->getUserQuestIds();
		if(!$user_quest_have_ids) {
			return array();
		}
		$user = $this->app->webUser;

		$builder = UserQuest::from('user_quest as uq')
			->join('quest_list as ql', 'ql.id', '=', 'uq.quest_id')
			->join('quest_dialog as qd', 'qd.id', '=', 'uq.custom_dialog_id')
			->join('user_dialog as ud', function($join) use ($user) {
				$join->on('ud.quest_id', '=', 'qd.global_parent_id')
					->on('ud.dialog_id', '=', 'qd.id')
					->where('ud.user_id', '=', $user->getId());
			})
			->where(function($query) use ($user) {
				$query->where('qd.location', '=', LocationHelper::ROOM_ALL)
					->orWhere('qd.location', '=', $user->getRoom());
			})
			->whereIn('uq.id', $user_quest_have_ids)
			->where('qd.bot_id', '=', $this->bot_id)
			->whereRaw('(ud.state is null or ud.state = 1) and ql.is_deleted = 0 and uq.custom_dialog_id > 0')
			->when(!$isAdmin, function($query) {
				$query->where('ql.is_enabled', '=', 1);
			})
			->select(['uq.quest_id as global_parent_id', 'uq.custom_dialog_id as id', 'ql.name as quest_name', 'qd.bot_id']);

		$result = $builder->get()->toArray();
		return $result;
	}
}