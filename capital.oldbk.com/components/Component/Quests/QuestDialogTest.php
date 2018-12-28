<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Component\Quests;

use components\Component\Config;
use components\Component\VarDumper;
use \components\models\quest\QuestDialog as Dialog;

class QuestDialogTest extends QuestDialogNew
{
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
					'state'		=> 'end',
				);
				$sorting[count($response_temp) - 1] = $Dialog['quest_name'];

				$allow_dialog_ids[] = $Dialog['id'];
			}
		}

		//ѕолучаем новые диалоги дл€ старта квеста
		if($this->start_dialogs && $Temp = $this->getStartDialogs()) {
			foreach ($Temp as $Dialog) {
				if($this->getQuestObj()->canGetByID($Dialog['global_parent_id']) === false) {
					continue;
				}


				if(in_array($Dialog['id'], $allow_dialog_ids)) {
					continue;
				}

				$response_temp[] = array(
					'dialog'    => $Dialog['id'],
					'title'     => $Dialog['quest_name'],
					'bot_id'	=> $Dialog['bot_id'],
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

		if(!isset($_SESSION['allow_dialog_ids'])) {
			$_SESSION['allow_dialog_ids'] = [];
		}

		$_SESSION['allow_dialog_ids'][$this->bot_id] = $allow_dialog_ids;

		return $response;
	}

	protected function getStartDialogs()
	{
		$isAdmin = (int)Config::admins();
		$quest_have_ids = $this->getQuestObj()->getQuestIds();

		$builder = Dialog::from('quest_dialog as qd')
			->join('quest_list as ql', 'ql.id', '=', 'qd.global_parent_id')
			->where('qd.bot_id', '=', $this->bot_id)
			->where('qd.item_type', '=', Dialog::TYPE_QUEST)
			->whereRaw('ql.is_deleted = 0 and qd.order_position = 1')
			->when(!$isAdmin, function($query) {
				/** @var mixed $query */
				$query->where('ql.is_enabled', '=', 1);
			})
			->when($quest_have_ids, function($query) use ($quest_have_ids) {
				/** @var mixed $query */
				$query->whereNotIn('qd.global_parent_id', $quest_have_ids);
			})
			->select(['qd.global_parent_id', 'qd.id', 'ql.name as quest_name', 'qd.bot_id']);

		return $builder->get()->toArray();
	}
}