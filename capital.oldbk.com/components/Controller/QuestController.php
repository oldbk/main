<?php
namespace components\Controller;
use components\Component\Config;
use components\Component\Db\CapitalDb;
use components\Component\Quests\QuestManual;
use components\Component\Quests\QuestTest;
use components\Component\VarDumper;
use \components\Controller\_base\MainController;
use components\models\Battle;
use components\models\Chat;
use components\models\quest\QuestList;
use components\models\quest\UserQuest;
use components\models\quest\UserQuestPart;
use components\models\quest\UserQuestPartItem;
use components\models\User;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 *
 */
class QuestController extends MainController
{
	public function dfinishAction()
	{
		/** @var User[] $UsersTo */
		$UsersTo = User::whereIn('id', [7937, 14897, 8540, 182783, 546433]);

		/** @var UserQuest[] $QuestList */
		$QuestList = UserQuest::where('quest_id', '=', 49)
			->where('is_finished', '=', 0)
			->where('is_cancel', '=', 0)
			->where('is_end', '=', 0)
			->get();
		foreach ($QuestList as $_Quest) {
			$User = User::find($_Quest->user_id);
			if(!$User) {
				continue;
			}

			$Quest = $this->app->quest->setUser($User)->get();

			$Checker = new \components\Component\Quests\check\CheckerGift();
			$Checker->shop_id = \components\Helper\ShopHelper::TYPE_ALL;
			$Checker->item_id = 30027;
			$Checker->operation_type = \components\Component\Quests\pocket\questTask\GiftTask::OPERATION_TYPE_FSHOP;
			foreach ($UsersTo as $UserTo) {
				$Checker->user_to = $UserTo;
				if (($Item = $Quest->isNeed($Checker)) !== false) {
					$Quest->taskUp($Item);
				}
			}


			$Checker = new \components\Component\Quests\check\CheckerEvent();
			$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_RIST_DO;
			if (($Item = $Quest->isNeed($Checker)) !== false) {
				$Quest->taskUp($Item);
			}
		}
	}

	public function cancelAction()
	{
		try {
			$id = $this->app->request->get('id', null);
			if(!$id) {
				throw new \Exception;
			}

			$Quest = $this->app->quest->get();
			if(!$Quest->cancelUserQuest($id)) {
				throw new \Exception;
			}

		} catch (\Exception $ex) {
		}

		$this->redirect('/main.php?edit=1&effects=1#quests');
	}

	public function dailyAction()
	{
		$this->renderJSON(array(
			'redirect' => '/city.php'
		));

		$haveQuest = false;
		$QuestComponent = $this->app->quest->get();

		$isAdmin = (int)Config::admins($this->app->webUser->getId());
		$current = new \DateTime();
		$current_string = $current->format('d.m.Y');
		$QuestList = QuestList::where('quest_type', '=', 'daily')
			->when(!$isAdmin, function($query) {
				$query->where('is_enabled', '=', 1);
			})
			->get()->toArray();

		foreach ($QuestList as $Quest) {
			if($QuestComponent->canGetByID($Quest['id']) === true) {
				$count = UserQuest::whereRaw('user_id = ? and quest_id = ? and is_cancel = 0', [$this->user->getId(), $Quest['id']])
					->whereRaw('DATE_FORMAT(FROM_UNIXTIME(created_at), "%d.%m.%Y") = ?', [$current_string])
					->count();
				if(!$count && $QuestComponent->addQuest($Quest['id']) === true) {
					$haveQuest = true;
				}
			}
		}

		if(!$haveQuest) {
			Chat::addToChatSystem('На сегодня для вас нет заданий!', $this->app->webUser->getUser());
		}
		$this->renderJSON(array(
			'redirect' => '/city.php'
		));
	}

	public function testAction()
	{
		$User = User::find(546433);
		$QuestObj = new QuestTest();
		$QuestObj->setUser($User)->get();

		$Checker = new \components\Component\Quests\check\CheckerDrop();
		$Checker->item_id = 506608;
		$Checker->shop_id = \components\Helper\ShopHelper::TYPE_ALL;
		if (($Item = $QuestObj->isNeed($Checker)) !== false) {
			$QuestObj->taskUp($Item);
		}
	}

	public function test2Action()
	{
		$User = User::find(546433);
		$QuestObj = new QuestTest();
		$QuestObj->setUser($User)->get();

		$Checker = new \components\Component\Quests\check\CheckerEvent();
		$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_FIGHT_HIT;
		$Checker->count = 50;

		$Battle = new \components\models\Battle();
		$Battle->damage = 1;
		$Battle->is_win = true;
		$Battle->type = 3;
		$Battle->coment = '<b>#zlevels</b>';

		$Checker->battle = $Battle;
		if ($Checker->count > 0 && ($Item = $QuestObj->isNeed($Checker)) !== false) {
			$QuestObj->taskUp($Item);
		}
	}

	public function test3Action()
	{
		$i = 0;
		try {
			$user_ids = [];
			$models = UserQuestPart::whereRaw('quest_part_id = 189 and started_at >= 1532978438 and is_finished = 0 and is_started = 1')->get();

			foreach ($models as $model) {
				if(!in_array($model->user_id, $user_ids)) {
					$user_ids[] = $model->user_id;
				}
			}

			foreach ($user_ids as $user_id) {
				$User = User::find($user_id);
				$QuestObj = new QuestManual();
				$QuestObj->setUser($User)->get();
				$r = $QuestObj->forceFinishPart(92, 189);
				if($r) {
					$i++;
				}
			}

		} catch (\Exception $ex) {
			VarDumper::d($ex->getMessage());
		}

		var_dump($i.PHP_EOL);
		var_dump(count($user_ids).PHP_EOL);
		var_dump('done');
	}

	public function test4Action()
	{
		$User = User::find(7937);
		$QuestObj = new QuestTest();
		$QuestObj->setUser($User)->get();

		$Battle = Battle::find(392585210);

		$Checker = new \components\Component\Quests\check\CheckerFight();
		$Checker->damage = 100;
		$Checker->is_win = false;
		$Checker->fight_type = $Battle->type;
		$Checker->fight_comment = $Battle->coment;
		$Checker->battle = $Battle;

		$item = $QuestObj->isNeed($Checker);
		$r = $QuestObj->taskUp($item);
		VarDumper::d($r, false);
		VarDumper::d($item);
	}

	public function test5Action()
	{
		$User = User::find(704957);

		$QuestManual= new QuestTest();
		$QuestManual = $QuestManual->setUser($User)->get();

		$quest_ids = $QuestManual->getUserQuestIds();
		VarDumper::d($quest_ids);
	}
}