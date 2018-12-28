<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Component\Quests;


use components\Component\Config;
use components\Component\Slim\Slim;
use components\Component\VarDumper;
use components\models\quest\QuestDialogAction;
use components\models\quest\QuestList;
use \components\models\quest\QuestDialog as Dialog;

/**
 * Class QuestDialog
 * @package components\Component\Quests
 * @deprecated
 */
class QuestDialog
{
	protected $app;
	protected $bot_id = 0;
	/** @var Quest|QuestTest */
	protected $quest;

	public function __construct($bot_id)
	{
		$this->app = Slim::getInstance();
		$this->bot_id = $bot_id;
	}

	/**
	 * @return QuestManual
	 */
	protected function getQuestObj()
	{
		if(!$this->quest) {
			$QuestManual= new QuestManual();
			$this->quest = $QuestManual->get();

		}

		return $this->quest;
	}

	public function getMainDialog()
	{
		$response = array();
		$allow_dialog_ids = array();
		$DialogList = array();

		//Получаем диалоги для завершения квеста
		$Temp = $this->getEndDialogs();
		if($Temp) {
			$end_quest_ids = array();
			foreach ($Temp as $Dialog) {
				$end_quest_ids[] = $Dialog['item_id'];
				$DialogList[$Dialog['item_id']] = $Dialog;
			}
			$Temp = QuestList::whereIn('id', $end_quest_ids)->get()->toArray();
			foreach ($Temp as $Quest) {
				$Dialog = $DialogList[$Quest['id']];
				$response[] = array(
					'dialog'    => $Dialog['id'],
					'title'     => $Quest['name']
				);

				$allow_dialog_ids[] = $Dialog['id'];
			}
		}

		//Получаем новые диалоги для старта квеста
		$Temp = $this->getStartDialogs();
		if($Temp) {
			$start_quest_ids = array();
			foreach ($Temp as $Dialog) {
				$start_quest_ids[] = $Dialog['item_id'];
				$DialogList[$Dialog['item_id']] = $Dialog;
			}
			$Temp = QuestList::whereIn('id', $start_quest_ids)->get()->toArray();
			foreach ($Temp as $Quest) {
				if($this->getQuestObj()->canGet($Quest) === false) {
					continue;
				}

				$Dialog = $DialogList[$Quest['id']];
				$response[] = array(
					'dialog'    => $Dialog['id'],
					'title'     => $Quest['name']
				);

				$allow_dialog_ids[] = $Dialog['id'];
			}
		}

		$_SESSION['allow_dialog_ids'] = $allow_dialog_ids;

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
			->when($quest_have_ids, function($query) use ($quest_have_ids) {
				/** @var mixed $query */
				$query->whereNotIn('qd.global_parent_id', $quest_have_ids);
			})
			->when(!$isAdmin, function($query) {
				/** @var mixed $query */
				$query->where('ql.is_enabled', '=', 1);
			})
			->select(['qd.*']);

		return $builder->get()->toArray();
	}

	protected function getEndDialogs()
	{
		$isAdmin = (int)Config::admins();
		$quest_have_ids = $this->getQuestObj()->getQuestIds();
		if(!$quest_have_ids) {
			return array();
		}

		$builder = Dialog::from('quest_dialog as qd')
			->join('quest_list as ql', 'ql.id', '=', 'qd.global_parent_id')
			->where('qd.bot_id', '=', $this->bot_id)
			->where('qd.item_type', '=', Dialog::TYPE_QUEST)
			->where('qd.action_type', '=', Dialog::ACTION_QUEST_END)
			->whereIn('qd.global_parent_id', $quest_have_ids)
			->whereRaw('ql.is_deleted = 0')
			->when(!$isAdmin, function($query) {
				/** @var mixed $query */
				$query->where('ql.is_enabled', '=', 1);
			})
			->select(['qd.*']);

		return $builder->get()->toArray();
	}

	protected function getDialog($id)
	{
		$isAdmin = (int)Config::admins();

		$Dialog = Dialog::from('quest_dialog as qd')
			->join('quest_list as ql', 'ql.id', '=', 'qd.global_parent_id')
			->where('qd.id', '=', $id)
			->where('qd.bot_id', '=', $this->bot_id)
			->whereRaw('ql.is_deleted = 0')
			->when(!$isAdmin, function($query) {
				/** @var mixed $query */
				$query->where('ql.is_enabled', '=', 1);
			})
			->select(['qd.*'])->first();
		if($Dialog) {
			return $Dialog->toArray();
		}

		return false;
	}

	protected function getActions($dialog_id)
	{
		return QuestDialogAction::whereRaw('dialog_id = ?', [$dialog_id])->get()->toArray();
	}

	public function dialog($dialog_id, $action_id)
	{
		if($dialog_id === null) {
			return false;
		}

		$allow_dialog_ids = array();
		if(!in_array($dialog_id, $_SESSION['allow_dialog_ids'])) {
			return false;
		}

		$Dialog = $this->getDialog($dialog_id);
		if(!$Dialog) {
			return false;
		}

		switch ($Dialog['action_type']) {
			case Dialog::ACTION_QUEST_START:
				if($this->getQuestObj()->addQuest($Dialog['item_id']) === false) {
					return $this->error('Хм... Не в этот раз');
				}
				break;
			case Dialog::ACTION_QUEST_END:
				if($this->getQuestObj()->manualFinishQuest($Dialog['item_id']) === false) {
					return $this->error('У вас есть незавершенные задания, посмотреть подробнее можно в разделе «<a href="/main.php?edit=1&effects=1#quests">Состояние</a>»');
				}
				break;
		}

		$actions = array();
		$Temp = $this->getActions($dialog_id);
		foreach ($Temp as $Action) {
			$actions[] = array(
				'dialog' => $Action['next_dialog_id'],
				'action' => $Action['id'],
				'message' => $this->prepareMessage($Action['message'])
			);
			if($Action['next_dialog_id'] != null) {
				$allow_dialog_ids[] = $Action['next_dialog_id'];
			}
		}

		$_SESSION['allow_dialog_ids'] = $allow_dialog_ids;
		return array(
			'message' => $this->prepareMessage($Dialog['message']),
			'actions' => $actions
		);
		//НАжали на ответ
	}

	protected function error($message)
	{
		return array(
			'message' => $message,
			'actions' => array(
				array(
					'dialog' => null,
					'action' => 0,
					'message' => 'Зайти позже'
				)
			)
		);
	}

	protected function prepareMessage($message)
	{
		$i = 0;
		while(true) {
			$i++;
			if(!preg_match('/\{gender\:(.*?)\}/i', $message, $out)) {
				break;
			}

			$sex = explode('|', $out[1]);
			if($this->app->webUser->getGender() == 1) {
				$message = str_replace('{gender:'.$out[1].'}', $sex[0], $message);
			} else {
				$message = str_replace('{gender:'.$out[1].'}', $sex[1], $message);
			}

			if($i == 10) {
				break;
			}
		}

		return $message;
	}
}