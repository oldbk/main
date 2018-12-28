<?php
namespace components\Controller;
use components\Component\Config;
use components\Component\Db\CapitalDb;
use components\Component\Quests\Quest;
use components\Component\Quests\QuestTest;
use components\Component\VarDumper;
use \components\Controller\_base\AdminController;
use components\Helper\BadgeGiveHelper;
use components\Helper\FightHelper;
use components\Helper\FileHelper;
use components\Helper\item\ItemEkr;
use components\Helper\item\ItemExp;
use components\Helper\item\ItemItem;
use components\Helper\item\ItemRep;
use components\Helper\ItemHelper;
use components\Helper\rating\FontanRating;
use components\models\Inventory;
use components\models\ItemLoto;
use components\models\NewDelo;
use components\models\quest\UserQuest;
use components\models\quest\UserQuestPart;
use components\models\quest\UserQuestPartItem;
use components\models\Shop;
use components\models\User;
use components\models\UserEventRating;
use Intervention\Image\ImageManager;
use components\Component\Db\CapitalDb as DB;
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 *
 */
class FixesController extends AdminController
{
	public function tqAction()
	{
		$User = User::where('id', '=', 546433)->first();

		for($i = 0; $i < 1000; $i++) {
			$OsadaRating = new \components\Helper\rating\OsadaRating();
			$OsadaRating->value_add = 1;


			$this->app->applyHook('event.rating', $User, $OsadaRating);
		}

		var_dump('done');
	}

	public function Quest123Action()
	{
		/** @var UserQuest[] $UserQuest */
		$UserQuest = UserQuest::where('is_finished', '=', 0)
			->where('is_cancel', '=', 0)
			->where('is_end', '=', 0)
			->where('quest_id', '=', 64)
			->get();
		foreach ($UserQuest as $_user_quest) {
			$UserQuestPart = UserQuestPart::where('user_quest_id', '=', $_user_quest->id)
				->where('is_started', '=', 1)
				->first();
			if(!$UserQuestPart) {
				continue;
			}

			$db = CapitalDb::connection();
			try {
				UserQuestPartItem::where('user_quest_id', '=', $_user_quest->id)
					->where('is_deleted', '=', 1)
					->update([
						'is_deleted' => 0
					]);

				$_temp = [];
				/** @var UserQuestPartItem[] $UserQuestPartItems */
				$UserQuestPartItems = UserQuestPartItem::where('user_quest_part_id', '=', $UserQuestPart->id)->get();
				foreach ($UserQuestPartItems as $_item) {
					if(!isset($_temp[$_item->item_id])) {
						$_temp[$_item->item_id] = [
							'count' => 0,
							'is_finished' => 0,
							'id' => $_item->id,
						];
					} else {
						UserQuestPartItem::where('id', '=', $_item->id)
							->update(['is_deleted' => 1]);
						$_temp[$_item->item_id]['count'] += 1;
					}

					$_temp[$_item->item_id]['count'] += $_item->count;
					if($_temp[$_item->item_id]['count'] >= $_item->need_count) {
						$_temp[$_item->item_id]['count'] = $_item->need_count;
						$_temp[$_item->item_id]['is_finished'] = 1;
					}
				}

				foreach ($_temp as $item_id => $values) {
					UserQuestPartItem::where('id', '=', $values['id'])
						->update([
							'count' => $values['count'],
							'is_finished' => $values['is_finished'],
						]);
				}

				$db->commit();
			} catch (\Exception $ex) {
				$db->rollBack();
				//var_dump('error');
			}
		}
		VarDumper::d(count($UserQuest));
	}

	public function lAction()
	{
		Inventory::whereRaw('prototype = ? and upfree = ?', [33333, 190])->delete();
	}

	public function lotoAction()
	{
		return;
		$db = CapitalDb::connection();
		try {
			$db->beginTransaction();

			$items_count = [];
			/** @var ItemLoto[] $Tickets */
			$Tickets = ItemLoto::whereRaw('loto = 220 and owner != 477431')->get();
			foreach ($Tickets as $Ticket) {
				if(!isset($items_count[$Ticket->owner])) {
					$items_count[$Ticket->owner]['count'] = 0;
				}
				$items_count[$Ticket->owner]['count']++;
				/*if(!$Item->delete()) {
					throw new \Exception('2');
				}*/
			}

			$Inventory = Inventory::where('id', '=', 1046109043)->first()->toArray();
			unset($Inventory['id']);

			echo '<pre>';
			foreach ($items_count as $user_id => $info) {
				if(!$info['count']) {
					continue;
				}
				$Inventory['owner'] = $user_id;

				var_dump($user_id.': '.$info['count']);

				/** @var User $User */
				$User = User::find($user_id);

				$item_ids = [];
				for($i = 1; $i <= $info['count']; $i++) {
					$ItemLoto = new ItemLoto();
					$ItemLoto->loto = 221;
					$ItemLoto->owner = $user_id;
					$ItemLoto->saletime = time();
					$ItemLoto->lotodate = '2018-10-14 20:00:00';
					if(!$ItemLoto->save()) {
						throw new \Exception();
					}

					$Inventory['mffree'] = $ItemLoto->id;
					$item_ids[] = Inventory::insertGetId($Inventory);

				}

				$item_id_string = '';
				foreach ($item_ids as $item_id) {
					$item_id_string .= ItemHelper::getItemId($User->id_city, $item_id).',';
				}
				$item_id_string = trim($item_id_string, ',');

				$_data = [
					'owner'                 => $User->id,
					'owner_login'           => $User->login,
					'owner_balans_do'       => $User->money,
					'owner_balans_posle'    => $User->money,
					'target'                => 0,
					'target_login'          => 'Администрация',
					'type'                  => 455,
					'sum_kr'                => 0,
					'sum_ekr'               => 0,
					'sum_kom'               => 0,
					'item_id'               => $item_id_string,
					'item_proto'            => 33333,
					'item_name'             => 'Купон ОлдБК',
					'item_count'            => $info['count'],
					'item_type'             => 210,
					'item_cost'             => '0.00',
					'item_ecost'            => '2.00',
					'item_dur'              => 0,
					'item_maxdur'           => 1,
					'item_ups'              => 0,
					'item_unic'             => 0,
					'item_incmagic'         => '',
					'item_incmagic_count'   => '',
					'item_arsenal'          => '',
					'sdate'                 => time(),
				];
				if(!NewDelo::addNew($_data)){
					throw new \Exception('3');
				}
			}

			$db->commit();
		} catch (\Exception $ex) {
			$db->rollBack();

			echo '<pre>';
			var_dump($ex);
		}
	}

	public function test1Action()
	{
		$User = User::find(578472);
		$Quest = $this->app->quest->setUser($User)->get();

		$BotDialog = new \components\Component\Quests\QuestDialogNew(\components\Helper\BotHelper::BOT_NASTAVNICK);
		$BotDialog->setQuestObj($Quest);
		$d = $BotDialog->getMainDialog();

		echo '<pre>';
		var_dump($Quest->getQuest()->getItemsArray());
		var_dump($d);

		$r = $Quest->canGetByID(19);
		var_dump($r);
	}

    public function beforeAction($action)
    {
        $r = parent::beforeAction($action); // TODO: Change the autogenerated stub
        //if($this->user->klan != 'radminion' && $this->user->klan != 'pal') {
        //    var_dump('false');die;
        //}

        return $r;
    }

    public function checkerAction()
	{
		$data = $this->app->configKo->getList();
		VarDumper::dump($data);
	}

    public function configAction()
	{
		$User = User::find(546433);
		$QuestObj = $this->app->quest->get();
		$Part = $QuestObj->getUserQuestObj()->getPart(79, 167);
		VarDumper::dump($Part->giveReward($User));
		//$Part->giveReward($this->user)
	}

    public function fortuneAction()
	{
		return;
		$fxarr  = ['100199','100420','100430','200440'];

		$Stats = UserFortuneStats::findAll('date >= 1504013400 and date <= 1504043580 and owner != 546433')->asArray();
		/** @var DB $db */
		$db->beginTransaction();
		try {
			foreach ($Stats as $_Stat) {
				$Owner = User::findByPk($_Stat['owner'])->asModel();
				$prototype = Shop::findByPk($_Stat['itemproto'])->asArray();

				$params = [
					'labonly' => 0,
					'labflag' => 0,
					'getfrom' => 144,
					'present' => 'Колесо Фортуны',
					'notsell' => 1
				];

				if (!$prototype['goden']) {
					$params['goden'] = 30;
					if ($_Stat['status'] == 3) {
						$params['goden'] = 60;
					}
					if ($_Stat['status'] == 4) {
						$params['goden'] = 90;
					}
					if ($_Stat['status'] == 5) {
						$params['goden'] = 180;
					}
				} elseif ($_Stat['status'] == 5 && in_array($prototype['id'], $fxarr)) {
					$params['goden'] = 180;
				}


				$item = ItemHelper::baseFromPrototype(array(), $prototype, $params);
				$item_ids = [];
				for($i = 0; $i < $_Stat['itemcount']; $i++) {
					$GiveItem = new ItemItem($Owner, $item);
					if(($item_id = $GiveItem->give()) === false) {
						throw new \Exception();
					}
					$item_ids[] = $item_id;
				}

				$item_id_string = '';
				foreach ($item_ids as $item_id) {
					$item_id_string .= ItemHelper::getItemId($Owner->id_city, $item_id).',';
				}
				$item_id_string = trim($item_id_string, ',');
				$_data = array(
					'owner'                 => $Owner->id,
					'owner_login'           => $Owner->login,
					'owner_balans_do'       => $Owner->money,
					'owner_balans_posle'    => $Owner->money,
					'item_count'            => $_Stat['itemcount'],
					'sdate'                 => time(),
					'target_login'          => 'Колесо Фортуны',
					'type'                  => 1991,
					'item_id'               => $item_id_string,
					'item_proto'            => $prototype['id'],
					'item_name'             => $item['name'],
					'item_type'             => $item['type'],
					'item_cost'             => $item['cost'],
					'item_ecost'            => $item['ecost'],
					'item_dur'              => $item['duration'],
					'item_maxdur'           => $item['maxdur'],
					'item_mfinfo'           => $item['mfinfo'] ? $item['mfinfo'] : '',
					'item_level'            => $item['nlevel'],
				);

				if(NewDelo::addNew($_data) === false) {
					throw new \Exception();
				}
			}

			$db->commit();
		} catch (\Exception $ex) {
			$db->rollBack();
		}
		var_dump(count($Stats));die;
	}

    public function battleAction()
	{
		return;
		$gifts = [
			1 => [
				['id' => 4170, 'count' => 1, 'goden' => 180], //Повышенный опыт (+100%)
				['id' => 4016, 'count' => 1, 'dur' => 3, 'goden' => 180], //Пропуск к Лорду Разрушителю
				['id' => 33055, 'count' => 1, 'goden' => 180], //Большой ужин викинга
				['id' => 33053, 'count' => 1, 'goden' => 180], //Большой ужин дракона
				['id' => 200277, 'count' => 20, 'goden' => 180], //Средний свиток «Восстановление 720HP»
				['id' => 19108, 'count' => 1, 'goden' => 180], //Большой свиток «Рунный опыт»
				['id' => 11301, 'count' => 1, 'dur' => 5, 'goden' => 180], //Невидимость
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III
				['id' => 535, 'count' => 1, 'goden' => 180], //Сертификат на уникальный подарок
				['id' => 33333, 'count' => 2], //Купон ОлдБК
			],
			2 => [
				['id' => 4169, 'count' => 1, 'goden' => 180], //Повышенный опыт (+90%)
				['id' => 4016, 'count' => 1, 'dur' => 3, 'goden' => 180], //Пропуск к Лорду Разрушителю
				['id' => 33055, 'count' => 1, 'goden' => 180], //Большой ужин викинга
				['id' => 33053, 'count' => 1, 'goden' => 180], //Большой ужин дракона
				['id' => 200277, 'count' => 20, 'goden' => 180], //Средний свиток «Восстановление 720HP»
				['id' => 19108, 'count' => 1, 'goden' => 180], //Большой свиток «Рунный опыт»
				['id' => 11301, 'count' => 1, 'dur' => 5, 'goden' => 180], //Невидимость
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III
				['id' => 533, 'count' => 1, 'goden' => 180], //Сертификат на личный смайл
				['id' => 33333, 'count' => 2], //Купон ОлдБК
			],
			3 => [
				['id' => 4168, 'count' => 1, 'goden' => 180], //Повышенный опыт (+80%)
				['id' => 4016, 'count' => 1, 'dur' => 3, 'goden' => 180], //Пропуск к Лорду Разрушителю
				['id' => 33055, 'count' => 1, 'goden' => 180], //Большой ужин викинга
				['id' => 33053, 'count' => 1, 'goden' => 180], //Большой ужин дракона
				['id' => 200277, 'count' => 20, 'goden' => 180], //Средний свиток «Восстановление 720HP»
				['id' => 19108, 'count' => 1, 'goden' => 180], //Большой свиток «Рунный опыт»
				['id' => 11301, 'count' => 1, 'dur' => 5, 'goden' => 180], //Невидимость
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III
				['id' => 33333, 'count' => 2], //Купон ОлдБК
			],
			4 => [
				['id' => 4167, 'count' => 1, 'goden' => 180], //Повышенный опыт (+70%)
				['id' => 4016, 'count' => 1, 'dur' => 3, 'goden' => 180], //Пропуск к Лорду Разрушителю
				['id' => 33054, 'count' => 1, 'goden' => 180], //Малый ужин викинга
				['id' => 33052, 'count' => 1, 'goden' => 180], //Малый ужин дракона
				['id' => 200277, 'count' => 15, 'goden' => 180], //Средний свиток «Восстановление 720HP»
				['id' => 11301, 'count' => 1, 'dur' => 5, 'goden' => 180], //Невидимость
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III
				['id' => 33333, 'count' => 1], //Купон ОлдБК
			],
			5 => [
				['id' => 4166, 'count' => 1, 'goden' => 180], //Повышенный опыт (+60%)
				['id' => 4016, 'count' => 1, 'dur' => 3, 'goden' => 180], //Пропуск к Лорду Разрушителю
				['id' => 33054, 'count' => 1, 'goden' => 180], //Малый ужин викинга
				['id' => 33052, 'count' => 1, 'goden' => 180], //Малый ужин дракона
				['id' => 200273, 'count' => 15, 'goden' => 180], //Большой свиток «Восстановление 360HP»
				['id' => 125125, 'count' => 1, 'dur' => 5, 'goden' => 180], //Лечение травм
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III
				['id' => 33333, 'count' => 1], //Купон ОлдБК
			],
			6 => [
				['id' => 4165, 'count' => 1, 'goden' => 180], //Повышенный опыт (+50%) PROTO_ID:4165 1шт.
				['id' => 4016, 'count' => 1, 'dur' => 3, 'goden' => 180], //Пропуск к Лорду Разрушителю PROTO_ID:4016 0/3 юза, 1шт.
				['id' => 33054, 'count' => 1, 'goden' => 180], //Малый ужин викинга PROTO_ID:33054 1шт.
				['id' => 33052, 'count' => 1, 'goden' => 180], //Малый ужин дракона PROTO_ID:33052 1шт.
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III PROTO_ID:56664 1шт.
				['id' => 200273, 'count' => 15, 'goden' => 180], //Большой свиток «Восстановление 360HP» PROTO_ID:200273, 15шт.
			],
			7 => [
				['id' => 4164, 'count' => 1, 'goden' => 180], //Повышенный опыт (+40%) PROTO_ID:4164 1шт.
				['id' => 4016, 'count' => 1, 'dur' => 3, 'goden' => 180], //Пропуск к Лорду Разрушителю PROTO_ID:4016 0/3 юза, 1шт.
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III PROTO_ID:56664 1шт.
				['id' => 200273, 'count' => 15, 'goden' => 180], //Большой свиток «Восстановление 360HP» PROTO_ID:200273, 15шт.
			],
			8 => [
				['id' => 4163, 'count' => 1, 'goden' => 180], //Повышенный опыт (+30%) PROTO_ID:4163
				['id' => 4016, 'count' => 1, 'dur' => 3, 'goden' => 180], //Пропуск к Лорду Разрушителю PROTO_ID:4016 0/3 юза, 1шт.
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III PROTO_ID:56664
				['id' => 200273, 'count' => 15, 'goden' => 180], //Большой свиток «Восстановление 360HP» PROTO_ID:200273, 15шт.
			],
			9 => [
				['id' => 4162, 'count' => 1, 'goden' => 180], //Повышенный опыт (+20%) PROTO_ID:4162 1шт.
				['id' => 4016, 'count' => 1, 'dur' => 3, 'goden' => 180], //Пропуск к Лорду Разрушителю PROTO_ID:4016 0/3 юза, 1шт.
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III PROTO_ID:56664 1шт.
				['id' => 200273, 'count' => 15, 'goden' => 180], //Большой свиток «Восстановление 360HP» PROTO_ID:200273, 15шт.
			],
			10 => [
				['id' => 4161, 'count' => 1, 'goden' => 180], //Повышенный опыт (+10%) PROTO_ID:4161 1шт.
				['id' => 56664, 'count' => 1, 'goden' => 180], //Великое Чарование III PROTO_ID:56664 1шт.
				['id' => 200273, 'count' => 15, 'goden' => 180], //Большой свиток «Восстановление 360HP» PROTO_ID:200273, 15шт.
			]
		];
		$temp = [];
		foreach ($gifts as $position => $items) {
			foreach ($items as $item) {
				if(!in_array($item['id'], $temp)) {
					$temp[] = $item['id'];
				}
			}
		}
		$prototypes = [];
		$Items = Shop::findAll('id in ('.Shop::getIN($temp).')', $temp)->asArray();
		foreach ($Items as $Item) {
			$prototypes[$Item['id']] = $Item;
		}


		$winners = [
			1 => [],
			2 => [],
		];
		$present = [];
		$badges = [
			'images' => [
				1 => 'http://i.oldbk.com/i/svet_tma2017_02.png',
				2 => 'http://i.oldbk.com/i/svet_tma2017_01.png',
			],
			'alt' => [
				1 => 'За участие в исторической битве Тьма vs Свет 24.08.2017 на стороне Мироздателя!',
				2 => 'За участие в исторической битве Тьма vs Свет 24.08.2017 на стороне Мусорщика!',
			]
		];

		$user_by_login = [];
		$log = 337326646;
		$data = FightHelper::parseLog($log, true);
		foreach ($data as $team => $users) {
			foreach ($users as $user) {
				if(!isset($user['info'])) {
					continue;
				}
				if($user['info']['level'] > 15) {
					$present[$team] = $user['info']['login'];
					continue;
				}

				$damage = $user['damage']['total']['all'] + $user['damage']['magic']['total'];

				$user_by_login[$user['info']['login']] = $user['info'];
				$winners[$team][$user['info']['level']][$user['info']['login']] = $damage;
			}
		}

		$count_badge = 0;
		$count_gifts = 0;
		$response = [];
		/** @var \database\DB $db */
		$db->beginTransaction();
		try {
			foreach ($winners as $team => $data) {
				foreach ($data as $level => $users) {
					if($level == 21) {
						continue;
					}

					arsort($users);
					foreach ($users as $login => $damage) {
						$user_info = $user_by_login[$login];

						$_data = [
							'user_id' 		=> $user_info['id'],
							'img' 			=> $badges['images'][$team],
							'alt' 			=> $badges['alt'][$team],
							'is_enabled' 	=> 1,
							'show_time' 	=> 0,
							'rate_unique' 	=> UserBadge::TYPE_SVET_TMA_08,
						];
						/*$r = UserBadge::insert($_data);
						if(!$r) {
							throw new \Exception();
						}*/
						$count_badge++;
					}

					if(count($users) > 10) {
						$users = array_slice($users, 0, 10, TRUE);
					}
					$response[$team][$level] = $users;
				}
			}

			$db->rollBack();
		} catch (\Exception $ex) {
			$db->rollback();
			VarDumper::d('first', false);
			VarDumper::d($ex->getMessage(), false);
			VarDumper::d($ex->getTraceAsString());
		}

		/** @var \database\DB $db */
		$db->beginTransaction();
		try {
			foreach ($response as $team => $data) {
				echo '*Тима '.$team.'*<br>';
				ksort($data);
				foreach ($data as $level => $users) {
					if($level == 21) {
						continue;
					}
					echo '<b>'.$level.' Уровень:</b><br>';
					$count = 0;
					foreach ($users as $login => $damage) {
						if($damage <= 0) {
							continue;
						}
						$count++;
						echo $count.' - '.$login.'<br>';
						$user_info = $user_by_login[$login];

						$message = sprintf('Внимание! Вы получили призы за %d место в бою Мусорщика и Мироздателя, среди %d-х уровней.', $count, $level);
						Chat::addToChatSystem($message, ['login' => $login, 'room' => 0, 'id_city' => 0]);
						/*if(!$this->sendGift($gifts[$count], $user_info['id'], $prototypes, $present[$team])) {
							throw new \Exception();
						}*/
						$count_gifts++;
					}
					echo '<br>';
				}
			}

			$db->commit();
		} catch (\Exception $ex) {
			$db->rollback();
			VarDumper::d('second', false);
			VarDumper::d($ex->getMessage(), false);
			VarDumper::d($ex->getTraceAsString());
		}

		var_dump($count_badge);
		var_dump($count_gifts);
		die('done');
		VarDumper::d($response);
	}

	private function sendGift($gifts, $user_id, $prototypes, $present = 'Мусорщик')
	{
		$Owner = User::findByPk($user_id)->asModel();

		foreach ($gifts as $_item) {
			$params = [
				'goden' => $_item['goden'],
				'present' => $present,
				'notsell' => 1
			];

			switch ($_item['id']) {
				case 33333:

					$params['letter'] = sprintf('Следующий обмен купонов на подарки состоится 2017-09-03 20:00:00');
					$params['upfree'] = 177;

					$item = ItemHelper::baseFromPrototype(array(), $prototypes[$_item['id']], $params);
					$item_ids = [];
					for($i = 0; $i < $_item['count']; $i++) {
						$_data = [
							'loto' => 177,
							'owner' => $Owner->id,
							'lotodate' => '2017-09-03 20:00:00'
						];
						$bill_id = ItemLoto::insert($_data);
						if(!$bill_id) {
							throw new \Exception();
						}
						$item['mffree'] = $bill_id;

						$GiveItem = new ItemItem($Owner, $item);
						if(($item_id = $GiveItem->give()) === false) {
							throw new \Exception();
						}
						$item_ids[] = $item_id;
					}

					$item_id_string = '';
					foreach ($item_ids as $item_id) {
						$item_id_string .= ItemHelper::getItemId($Owner->id_city, $item_id).',';
					}
					$item_id_string = trim($item_id_string, ',');
					$_data = array(
						'owner'                 => $Owner->id,
						'owner_login'           => $Owner->login,
						'owner_balans_do'       => $Owner->money,
						'owner_balans_posle'    => $Owner->money,
						'item_count'            => $_item['count'],
						'sdate'                 => time(),
						'target_login'          => $present,
						'type'                  => 98,
						'item_id'               => $item_id_string,
						'item_proto'            => $_item['id'],
						'item_name'             => $item['name'],
						'item_type'             => $item['type'],
						'item_cost'             => $item['cost'],
						'item_ecost'            => $item['ecost'],
						'item_dur'              => $item['duration'],
						'item_maxdur'           => $item['maxdur'],
						'item_mfinfo'           => $item['mfinfo'] ? $item['mfinfo'] : '',
						'item_level'            => $item['nlevel'],
					);

					if(NewDelo::addNew($_data) === false) {
						throw new \Exception();
					}

					break;
				default:

					if(isset($_item['dur'])) {
						$params['maxdur'] = $_item['dur'];
					}
					$item = ItemHelper::baseFromPrototype(array(), $prototypes[$_item['id']], $params);
					$item_ids = [];
					for($i = 0; $i < $_item['count']; $i++) {
						$GiveItem = new ItemItem($Owner, $item);
						if(($item_id = $GiveItem->give()) === false) {
							throw new \Exception();
						}
						$item_ids[] = $item_id;
					}

					$item_id_string = '';
					foreach ($item_ids as $item_id) {
						$item_id_string .= ItemHelper::getItemId($Owner->id_city, $item_id).',';
					}
					$item_id_string = trim($item_id_string, ',');
					$_data = array(
						'owner'                 => $Owner->id,
						'owner_login'           => $Owner->login,
						'owner_balans_do'       => $Owner->money,
						'owner_balans_posle'    => $Owner->money,
						'item_count'            => $_item['count'],
						'sdate'                 => time(),
						'target_login'          => $present,
						'type'                  => 98,
						'item_id'               => $item_id_string,
						'item_proto'            => $_item['id'],
						'item_name'             => $item['name'],
						'item_type'             => $item['type'],
						'item_cost'             => $item['cost'],
						'item_ecost'            => $item['ecost'],
						'item_dur'              => $item['duration'],
						'item_maxdur'           => $item['maxdur'],
						'item_mfinfo'           => $item['mfinfo'] ? $item['mfinfo'] : '',
						'item_level'            => $item['nlevel'],
					);

					if(NewDelo::addNew($_data) === false) {
						throw new \Exception();
					}

					break;
			}
		}

		return true;
	}

    public function startAction()
	{
		return;
		$quest_id = $this->app->request->get('quest_id');
		if(!Config::admins()) {
			return;
		}

		$Quest = $this->app->quest->get();
		if($Quest->addQuest($quest_id)) {
			var_dump('done');
		} else {
			var_dump('error');
		}
		die;
	}

	public function test55Action()
	{
		return;
		$Quest = $this->app->quest->get();
		$Quest = $Quest->getQuestByIdTest(56);
		VarDumper::d($Quest);
	}

    public function loto_fixAction()
    {
		return;
        $List = array();
        $InventoryList = Inventory::findAll('prototype = 33333 and upfree != 152', array(), array('id', 'owner'))->asArray();
        foreach ($InventoryList as $Inventory) {
            if(!isset($List[$Inventory['owner']])) {
                $List[$Inventory['owner']] = array();
            }

            $List[$Inventory['owner']][] = $Inventory;
        }

        $db->beginTransaction();
        try {
            foreach ($List as $user_id => $InventoryList) {
                $_data = array(
                    'loto'      => 152,
                    'owner'     => $user_id,
                    'dil'       => 0,
                    'lotodate'  => '2017-03-19 20:00:00',
                    'win'       => 0
                );
                VarDumper::d($_data, false);
                $bilet_id = ItemLoto::insert($_data);
                if(!$bilet_id) {
                    throw new \Exception('can\'t insert bilet');
                }

                $_data = array(
                    'upfree' => 152,
                    'mffree' => $bilet_id,
                    'letter' => 'Следующий обмен купонов на подарки состоится 2017-03-19 20:00:00'
                );
                VarDumper::d($_data, false);
                if(Inventory::update($_data, 'owner = ? and id = ? and prototype = 33333 and upfree != 152', array($user_id, $InventoryList[0]['id'])) == false) {
                    throw new \Exception('Can\'t update inventory');
                }
                //echo sprintf('%s_%s', $user_id, $InventoryList[0]['id']).'<br>';
                //Inventory::delete('prototype = 33333 and upfree != 152 and owner = ? and id = ?', array($user_id, $InventoryList[0]['id']));
            }

            $db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();
        }

        VarDumper::d($List);
    }

    public function tt23Action()
    {
        BadgeGiveHelper::maslo();
    }

    public function tt24Action()
    {
		return;
        $Cache = $this->app->cache;
        $Cache->clean();
        VarDumper::d($Cache->stats());
    }

    public function tt53Action()
    {
		return;
        $rows = $db->createQuery()
            ->select('*')
            ->from('stol')
            ->where('count = 50 and stol = 23')
            ->execute()
            ->fetchAll();
        //570 572
        foreach ($rows as $row) {
            $UserQuestPartItem = UserQuestPartItem::find('user_id = ? and item_id in (570, 572) and count < 50', array($row['owner']), array('count'))->asArray();
            if(!$UserQuestPartItem) {
                continue;
            }

            $User = User::findByPk($row['owner'])->asModel();
            if(!$User) {
                continue;
            }
            $Quest = new Quest();
            $Quest->setUser($User)
                ->get();
            $need = 50 - $UserQuestPartItem['count'];
            for ($i = 0; $i < $need; $i++) {
                $Checker = new \components\Component\Quests\check\CheckerEvent();
                $Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_FONTAN;
                if(($Item = $Quest->isNeed($Checker)) !== false) {
                    $Quest->taskUp($Item);
                } else {
                    break;
                }
            }
        }
    }

    public function pp1Action()
    {
		return;
        $User = User::findByPk(7937)->asModel();
        $Quest = new QuestTest();
        $Quest->setUser($User)->get();

        $Checker = new \components\Component\Quests\check\CheckerMagic();
        $Checker->magic_id = 2026;
        if(($Item = $Quest->isNeed($Checker)) !== false) {
            $Quest->taskUp($Item);
            var_dump('зачлось');
        } else {
            var_dump('не зачлось');
        }
    }

    public function ttAction()
    {
		return;
        $image1 = $this->prepareImage('http://i.oldbk.com/i/sh/cloack_hero.gif');
        $image2 = $this->prepareImage('http://i.oldbk.com/i/sh/art_templ_4.gif');
        $image3 = $this->prepareImage('http://i.oldbk.com/i/sh/art_templ_21.gif');
        $image4 = $this->prepareImage('http://i.oldbk.com/i/sh/nit_t10_18078.gif');
        $image5 = $this->prepareImage('http://i.oldbk.com/i/sh/naruchi1.gif');

        $this->render('captcha', array(
            'image1' => $image1,
            'image2' => $image2,
            'image3' => $image3,
            'image4' => $image4,
            'image5' => $image5,
        ));
    }

    protected function prepareImage($link)
    {
		return;
        // create an image manager instance with favored driver
        $manager = new ImageManager(array('driver' => 'imagick'));
        $image1 = $manager->canvas(100, 100);
        $image2 = $manager->make($link)->rotate(rand(-100, 100))
            //->greyscale()
            ->colorize(0, 30, 0);

        $x = 50 - $image2->width() / 2;
        $y = 50 - $image2->height() / 2;
        $image1->insert($image2, 'top-left', $x, $y);


        return $image1->encode('data-url');
    }

    public function testAction()
    {
		return;
        $Quest = $this->app->quest->get();
        if($Quest->havePart(19, 28)) {
            $Checker = new \components\Component\Quests\check\CheckerEvent();
            $Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_FIGHT_HIT;
            $Checker->count = 57;
            $Battle = \components\Model\Battle::create(array());
            $Battle->damage = 100;
            $Battle->is_win = true;
            $Battle->coment = '<b>#zlevels</b>';
            $Battle->type = 3;

            $Checker->battle = $Battle;
            if ($Checker->count > 0 && ($Item = $Quest->isNeed($Checker)) !== false) {
                $Quest->taskUp($Item);
                var_dump('need');
            } else {
                var_dump('no');
            }
        }
    }

    public function test2Action()
    {
		return;
        $Quest = $this->app->quest->get();
        var_dump(get_class($Quest));
        $Checker = new \components\Component\Quests\check\CheckerDrop();
        $Checker->item_id = 506608;
        $Checker->shop_id = \components\Helper\ShopHelper::TYPE_ALL;
        if (($Item = $Quest->isNeed($Checker)) !== false) {
            $Quest->taskUp($Item);
        }
    }

    public function badgeAction()
    {
		return;
        $badges = array(
            array(
                'img' => 'http://i.oldbk.com/i/icon/bowling_1.gif',
                'desc' => 'За I место в турнире по Боулингу - 29.10.16',
                'users' => array(7331, 128030, 7436, 3757)
            ),
            array(
                'img' => 'http://i.oldbk.com/i/icon/bowling_2.gif',
                'desc' => 'За II место в турнире по Боулингу - 29.10.16',
                'users' => array(7382, 7387, 14830, 226631, 497992)
            ),
            array(
                'img' => 'http://i.oldbk.com/i/icon/bowling_3.gif',
                'desc' => 'За III место в турнире по Боулингу - 29.10.16',
                'users' => array(7108, 10633, 213343, 184504, 11862)
            ),
            array(
                'img' => 'http://i.oldbk.com/i/icon/bowling_participant.gif',
                'desc' => 'За участие в турнире по Боулингу - 29.10.16',
                'users' => array(368518, 352402, 10502, 453916, 103951, 423329, 493952, 215448, 31002, 216478,
                    511010, 90426, 89405, 38555, 9356, 9801, 588927, 339259, 6844)
            ),
            array(
                'img' => 'http://i.oldbk.com/i/icon/bowling_fan.gif',
                'desc' => 'Активному болельщику на турнире по Боулингу - 29.10.16',
                'users' => array(16929, 427416, 645864, 391476, 12600, 12473, 15316, 307042, 671526)
            ),
        );

        $db->beginTransaction();
        try {
            foreach ($badges as $badge) {
                foreach ($badge['users'] as $user_id) {
                    $data = array(
                        'user_id'           => $user_id,
                        'img'               => $badge['img'],
                        'description'       => null,
                        'alt'               => $badge['desc'],
                        'created_at'        => time(),
                        'is_enabled'        => 1,
                        'show_time'         => 0,
                        'rate_unique'       => UserBadge::TYPE_BOULING,
                    );

                    if(!UserBadge::insert($data)) {
                        throw new \Exception();
                    }
                }
            }

            $db->commit();
            var_dump('finish');
        } catch (\Exception $ex) {
            $db->rollBack();
            var_dump('error');
        }
    }

    public function test45Action()
    {
		try {
			$User = User::find(546433);
			$FontanRating = new FontanRating();
			$FontanRating->value_add = 1;

			$this->app->applyHook('event.rating', $User, $FontanRating);
		} catch (\Exception $ex) {
			VarDumper::dump($ex->getMessage());
		}
    }

    public function test33Action()
    {
		try {
			$r = CapitalDb::table('daily_free')
				->where('essence', '=', \components\models\DailyFree::ESSENCE_FONTAN)
				->whereRaw('used_total < limit_used_total') //меньше суточного лимита
				->whereRaw('uses < limit_uses') //меньше "порционного лимита"
				->whereRaw('(uses + used_total) < limit_used_total')
				->where('added_at', '<=', (new \DateTime())->modify('-6 minute')->getTimestamp()) //добавлялось более 6мин назад
				->where('is_finished', '=', 0)
				->increment('uses', 1, ['added_at' => (new \DateTime())->getTimestamp()]); //добавляем 1 юз

			var_dump($r);
		} catch (\Exception $ex) {
			var_dump($ex->getMessage());
		}
    }

	public function rewardAction()
	{
		return;
		$total_exp = 0;
		$total_rep = 0;
		$total_ekr = 0;
		echo '<pre>';
		$db = UserQuest::model()->db();

		$stmt = $db->select('count(*) as cnt, uq.user_id')
			->from('user_quest uq')
			->where('uq.is_finished = 1 and uq.quest_id in (7,8,10,11,12,13,14,15,16,17) and uq.created_at > 1488326400')
			->groupBy('uq.user_id')
			->having('count(*) > 4')
			->orderBy('cnt')
			->execute();
		$UserQuest = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		$user_ids = array();
		foreach ($UserQuest as $_item) {
			$user_ids[] = $_item['user_id'];
		}

		$Users = array();
		$temp = User::findAll('id in ('.User::getIN($user_ids).')', $user_ids)->asArray();
		foreach ($temp as $_item) {
			$Users[$_item['id']] = $_item;
		}
		$i = 0;


		/*$User = User::findByPk(546433)->asArray();
		$Users = array(
			546433 => $User
		);
		$UserQuest = array(
			array(
				'user_id' => 546433,
				'cnt' => 6
			)
		);*/

		foreach ($UserQuest as $_item) {
			if(!isset($Users[$_item['user_id']])) {
				continue;
			}

			$User = User::create($Users[$_item['user_id']]);

			if(QuestEventReward::count('event_id = 1 and user_id = ? and date_event = 201709', array($User->id))) {
				continue;
			}

			$count = (int)($_item['cnt'] / 5);

			$exp = 10000 * $count;
			$ekr = 5 * $count;
			$rep = 5000 * $count;

			$total_exp += $exp;
			$total_ekr += $ekr;
			$total_rep += $rep;

			$message = sprintf('<font color="red">Внимание!</font> Вы получили бонус за событие <b>«<a href="http://oldbk.com/encicl/?/action_august.html" target="_blank">Жаркий август</a>»</b> %d опыта, %d екр и %d репутации!',
				$exp, $ekr, $rep);

			echo sprintf('Пользователь: %s. Квесты: %d. Опыт: %d. Екр: %d. Репа: %d', $User->login, $_item['cnt'], $exp, $ekr, $rep).'<br>';
			$i++;

			$db->beginTransaction();
			try {

				//опыт награда
				$GiveExp = new ItemExp($User, $exp);
				if($GiveExp->give() === false) {
					throw new \Exception;
				}

				$_data = array(
					'target_login'          => 'Квест',
					'type'                  => NewDelo::TYPE_QUEST_REWARD_EXP,
					'add_info'              => 'Жаркий август',
				);

				if($GiveExp->newDeloGive($_data) === false) {
					throw new \Exception;
				}


				//Екр награда
				$GiveEkr = new ItemEkr($User, $ekr);
				if($GiveEkr->give() === false) {
					throw new \Exception;
				}

				$_data = array(
					'target_login'          => 'Квест',
					'type'                  => NewDelo::TYPE_QUEST_REWARD_EKR,
					'sum_ekr'               => $ekr,
					'add_info'              => 'Жаркий август',
				);

				if($GiveEkr->newDeloGive($_data) === false) {
					throw new \Exception;
				}

				//репа награда
				$GiveRep = new ItemRep($User, $rep);
				if($GiveRep->give() === false) {
					throw new \Exception;
				}

				$_data = array(
					'target_login'          => 'Квест',
					'type'                  => NewDelo::TYPE_QUEST_REWARD_REP,
					'add_info'              => 'Жаркий август',
				);

				if($GiveRep->newDeloGive($_data) === false) {
					throw new \Exception;
				}

				if($User->odate >= (time()-60)) {
					if(Chat::addToChatSystem($message, $User) === false) {
						throw new \Exception();
					}
				} else {
					if(Telegraph::add($User->id, $message) === false) {
						throw new \Exception();
					}
				}

				$_data = array(
					'event_id' => 1,
					'name' => 'Жаркий август',
					'user_id' => $_item['user_id'],
					'reward' => serialize(array(
						'exp' => $exp,
						'rep' => $rep,
						'ekr' => $ekr
					)),
					'created_at' => time(),
					'date_event' => 201709
				);
				if(QuestEventReward::insert($_data) === false) {
					throw new \Exception();
				}

				$db->commit();
			} catch (\Exception $ex) {
				$db->rollBack();
				FileHelper::writeException($ex, 'august reward');
				var_dump('Error');
			}
		}

		var_dump($i);
		var_dump($total_ekr);
		var_dump($total_exp);
		var_dump($total_rep);

	}
}