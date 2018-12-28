<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Component\Quests;


use components\Component\Config;
use components\Component\VarDumper;
use components\Helper\StringHelper;
use \components\models\quest\QuestDialog as Dialog;
use components\models\quest\UserQuest;

class QuestDialogNew extends QuestDialog
{
	protected $start_dialogs = true;
	protected $save_dialogs = true;
	protected $end_dialogs = true;

	public function setQuestObj($Quest)
	{
		$this->quest = $Quest;
		return $this;
	}

    /**
     * @return QuestManual
     */
    protected function getQuestObj()
    {
        if(!$this->quest) {
            if(Config::admins()) {
                $QuestManual= new QuestTest();
            } else {
                $QuestManual= new QuestManual();
            }

            $this->quest = $QuestManual->get();
        }

        return $this->quest;
    }

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

        //Получаем диалоги для завершения квеста
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

        //Получаем новые диалоги для старта квеста
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
			->select(['qd.global_parent_id', 'qd.id', 'ql.name as quest_name', 'qd.bot_id']);

		return $builder->get()->toArray();
    }

    protected function getSaveDialogs()
    {
        $isAdmin = (int)Config::admins();
        $user_quest_have_ids = $this->getQuestObj()->getUserQuestIds();
        if(!$user_quest_have_ids) {
            return array();
        }

		$builder = UserQuest::from('user_quest as uq')
			->join('quest_list as ql', 'ql.id', '=', 'uq.quest_id')
			->join('quest_dialog as qd', 'qd.id', '=', 'uq.dialog_id_save')
			->where('qd.bot_id', '=', $this->bot_id)
			->whereIn('uq.id', $user_quest_have_ids)
			->whereRaw('ql.is_deleted = 0 and uq.dialog_id_save > 0')
			->when(!$isAdmin, function($query) {
				/** @var mixed $query */
				$query->where('ql.is_enabled', '=', 1);
			})
			->select(['uq.quest_id as global_parent_id', 'uq.dialog_id_save as id', 'ql.name as quest_name', 'qd.bot_id']);

		return $builder->get()->toArray();
    }

    public function dialog($dialog_id, $action_id)
    {
        if($dialog_id === null) {
            return false;
        }

        $allow_dialog_ids = array();
        if(!isset($_SESSION['allow_dialog_ids'][$this->bot_id]) || empty($_SESSION['allow_dialog_ids'][$this->bot_id])) {
            $_SESSION['allow_dialog_ids'][$this->bot_id] = $this->getQuestObj()->getDialogsIds();
        }
        if(!in_array($dialog_id, $_SESSION['allow_dialog_ids'][$this->bot_id])) {
            return false;
        }

        $Dialog = $this->getDialog($dialog_id);
        if(!$Dialog) {
            return false;
        }

        switch ($Dialog['action_type']) {
            case Dialog::ACTION_QUEST_START:
                if($this->getQuestObj()->addQuest($Dialog['global_parent_id']) === false) {
                    return $this->error('Хм... Не в этот раз');
                }
                break;
            case Dialog::ACTION_PART_START:
                if($this->getQuestObj()->manualStartPart($Dialog['global_parent_id']) === false) {
                    return $this->error('У вас есть незавершенные задания, посмотреть подробнее можно в разделе «<a href="/main.php?edit=1&effects=1#quests">Состояние</a>»');
                }
                break;
            case Dialog::ACTION_PART_END:
                if($this->getQuestObj()->manualFinishPart($Dialog['global_parent_id']) === false) {
                    return $this->error('У вас есть незавершенные задания, посмотреть подробнее можно в разделе «<a href="/main.php?edit=1&effects=1#quests">Состояние</a>»');
                }
                break;
            case Dialog::ACTION_PART_NEXT_START:
                if($this->getQuestObj()->manualStartNextPart($Dialog['global_parent_id']) === false) {
                    return $this->error('У вас есть незавершенные задания, посмотреть подробнее можно в разделе «<a href="/main.php?edit=1&effects=1#quests">Состояние</a>»');
                }
                break;
            case Dialog::ACTION_QUEST_END:
                if($this->getQuestObj()->manualFinishQuest($Dialog['global_parent_id']) === false) {
                    return $this->error('У вас есть незавершенные задания, посмотреть подробнее можно в разделе «<a href="/main.php?edit=1&effects=1#quests">Состояние</a>»');
                }
                break;
        }

        if(is_numeric($Dialog['next_save_dialog'])) {
            $_data = array(
                'dialog_id_save' => $Dialog['next_save_dialog']
            );
            if($Dialog['next_save_dialog'] == 0) {
                $_data['custom_dialog_id'] = 0;
            }
			UserQuest::whereRaw('user_id = ? and quest_id = ? and is_finished = 0 and is_end = 0 and is_cancel = 0', [$this->app->webUser->getId(), $Dialog['global_parent_id']])
				->update($_data);
        }

        $actions = array();
        $Temp = $this->getActions($dialog_id);
        foreach ($Temp as $Action) {
            $actions[] = array(
            	'bot_id' => $Dialog['bot_id'],
                'dialog' => $Action['next_dialog_id'],
                'action' => $Action['id'],
                'message' => $this->prepareMessage($Action['message'])
            );
            if($Action['next_dialog_id'] != null) {
                $allow_dialog_ids[] = $Action['next_dialog_id'];
            }
        }

        $_SESSION['allow_dialog_ids'][$this->bot_id] = $allow_dialog_ids;
        return array(
            'message' => $this->prepareMessage($Dialog['message']),
            'actions' => $actions
        );
        //НАжали на ответ
    }

    /**
     * @param $location_id
     * @return array|bool
     */
    public function getLocationDialog($location_id)
    {
        $isAdmin = (int)Config::admins();

		$builder = UserQuest::from('user_quest as uq')
			->join('quest_list as ql', 'ql.id', '=', 'uq.quest_id')
			->join('quest_dialog as qd','qd.id', '=', 'uq.custom_dialog_id')
			->whereRaw('ql.is_deleted = 0 and uq.custom_dialog_id > 0')
			->when(!$isAdmin, function($query) {
				/** @var mixed $query */
				$query->where('ql.is_enabled', '=', 1);
			})
			->where(function($query) use ($location_id) {
				/** @var mixed $query */
				$query->where('qd.location', '=', 'ALL')
					->orWhere('qd.location', '=', $location_id);
			})
			->select(['uq.quest_id as global_parent_id', 'uq.custom_dialog_id as id', 'ql.name as quest_name']);

		$result = $builder->get()->toArray();
        if(!$result) {
            return false;
        }

        $allow_dialog_ids = $response = array();
        foreach ($result as $Dialog) {
            $response[] = array(
                'dialog'    => $Dialog['id'],
                'title'     => $Dialog['quest_name']
            );

            $allow_dialog_ids[] = $Dialog['id'];
        }

        if(!isset($_SESSION['allow_dialog_ids'])) {
            $_SESSION['allow_dialog_ids'][$this->bot_id] = array();
        }
        $_SESSION['allow_dialog_ids'][$this->bot_id] = array_merge($_SESSION['allow_dialog_ids'][$this->bot_id], $allow_dialog_ids);

        return $response;
    }

    public function buildCustomView($message, $actions)
    {
        $this->app->view()->setControllerId('common');
        return $this->app->view()->renderPartial('quest', array(
            'message' => $message,
            'actions' => $actions
        ));
    }

    public function prepareMessage($message)
    {
        return StringHelper::prepareGender($message, $this->app->webUser->getGender());
    }
}