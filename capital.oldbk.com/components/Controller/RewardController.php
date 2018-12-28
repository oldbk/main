<?php
namespace components\Controller;
use components\Component\Db\CapitalDb;
use components\Component\VarDumper;
use components\Controller\_base\AdminController;
use components\Helper\BadgeGiveHelper;
use components\Helper\FileHelper;
use components\Helper\item\ItemEkr;
use components\Helper\item\ItemExp;
use components\Helper\item\ItemGold;
use components\Helper\item\ItemItem;
use components\Helper\item\ItemRep;
use components\Helper\ItemHelper;
use components\models\Battle;
use components\models\Chat;
use components\models\Inventory;
use components\models\NewDelo;
use components\models\quest\QuestEventReward;
use components\models\quest\UserQuest;
use components\models\quest\UserQuestEvent;
use components\models\Shop;
use components\models\Telegraph;
use components\models\TopRate;
use components\models\User;
use components\models\UserBadge;
use okw\CF\CF;
use okw\CF\Exception\CFException;
use Guzzle\Http\Client;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 *
 */
class RewardController extends AdminController
{
    private $allow_ip = array(
        '88.198.205.126',
        '138.201.90.129',
        '178.151.80.59'
    );

    public function beforeAction($action)
    {
        if(!in_array($action, array('loto_simulation', 'loto', 'lotogive', 'lotomessage'))) {
            $r = parent::beforeAction($action);

            if(!$this->user || !in_array($this->user->klan, array('Adminion', 'radminion'))) {
                $this->errorUser();
            }

            return $r;
        } elseif(!in_array($this->app->request->getIp(), $this->allow_ip)) {
            die;
        }

        return true;
    }

    public function mayAction()
    {
        return;
        die;
        BadgeGiveHelper::may();
    }

    public function marchAction()
    {
        return;
        $db = CapitalDb::connection();

        $give_rows = TopRate::from('top_rate as tr')
            ->join('users as u', 'u.id', '=', 'tr.user_id')
            ->whereRaw('u.bot=0 AND u.klan!="radminion" and u.klan!="Adminion"')
            ->whereRaw('tr.action_type=? and tr.rate_type=?', [UserBadge::TYPE_MARCH8.'2018', 1])
            ->orderBy('rate_value', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->select(['tr.value as rate_value', 'u.*'])
            ->get()->toArray();

        $get_rows = TopRate::from('top_rate as tr')
            ->join('users as u', 'u.id', '=', 'tr.user_id')
            ->whereRaw('u.bot=0 AND u.klan!="radminion" and u.klan!="Adminion"')
            ->whereRaw('tr.action_type=? and tr.rate_type=?', [UserBadge::TYPE_MARCH8.'2018', 2])
            ->orderBy('rate_value', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->select(['tr.value as rate_value', 'u.*'])
            ->get()->toArray();

        $ended = new \DateTime('2019-03-12 23:59:59');
        //$ended->modify('+1 year');

        $db->beginTransaction();
        try {
            //$user = User::find(546433)->toArray();

            //$this->rewardMarch([$user], $ended, 'Топ Дарители. %d место', 1);
            $this->rewardMarch($give_rows, $ended, 'Топ Дарители. %d место', 1);
            $this->rewardMarch($get_rows, $ended, 'Топ привлекательности. %d место', 2);

            $db->commit();
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            $db->rollBack();
        }

        var_dump('finish');
    }

    public function halloweenAction()
    {
        $user_ids = [];
        /** @var Battle[] $Battles */
        $Battles = Battle::whereIn('id', [346365583, 348117193, 348117334, 348117491, 348117634, 348117802, 348386193, 348573178])->get();
        foreach ($Battles as $Battle) {
            $team1 = explode(';', $Battle->t1);
            $team2 = explode(';', $Battle->t2);
            foreach ([$team1, $team2] as $team) {
                foreach ($team as $user_id) {
                    if($user_id == 140117009 || in_array($user_id, $user_ids)) {
                        continue;
                    }

                    $user_ids[] = $user_id;
                }
            }
        }

        /** @var \components\models\User[] $Users */
        $Users = \components\models\User::whereIn('id', $user_ids)->get();

        $db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            foreach ($Users as $User) {
                $UserBadge = new \components\models\UserBadge();
                $UserBadge->user_id = $User->id;
                $UserBadge->img = 'http://i.oldbk.com/i/helloween_2011m2.gif';
                $UserBadge->description = 'Участнику боя на Halloween-2017';
                $UserBadge->rate_unique = \components\models\UserBadge::TYPE_HALLOWEEN;
                $UserBadge->save();
            }

            $db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();
            VarDumper::d($ex);
        }

        VarDumper::d('done');
    }

    private $marchReward = array(
        1 => 250,
        2 => 150,
        3 => 100,
        4 => 50,
        5 => 40,
        6 => 30,
        7 => 25,
        8 => 15,
        9 => 10,
        10 => 5
    );
    /**
     * @param $List
     * @param \DateTime $ended
     * @param $alt
     * @param $type
     * @throws \Exception
     */
    private function rewardMarch($List, $ended, $alt, $type)
    {
        $i = 0;
        $rate_image = 'http://i.oldbk.com/i/i/quest_0803_top%d_%s.png';
        foreach ($List as $user) {
            $i++;
            $img_num = 1;
            if($i > 10 && $i < 51) {
                $img_num = 2;
            } elseif($i > 50) {
                $img_num = 3;
            }
            $gender = 'm';
            if($user['sex'] == User::GENDER_FEMALE) {
                $gender = 'w';
            }
            $img = sprintf($rate_image, $img_num, $gender);

            $User = User::find($user['id']);

            $r = UserBadge::addOrUpdateExpire(
                $user['id'],
                $img,
                sprintf($alt, $i),
                $ended->getTimestamp(),
                UserBadge::TYPE_MARCH8.'_'.$type,
                'http://top.oldbk.com/rate/march8'
            );
            if($r === false) {
                throw new \Exception;
            }

            if($i < 11) {
                $count = $this->marchReward[$i];
            } elseif($i > 10 && $i < 51) {
                $count = 3;
            } else {
                $count = 1;
            }
            $message = sprintf('Поздравляем! Вы получили <strong>%d екр.</strong> за <strong>%d</strong> место в рейтинге события «<a href="http://top.oldbk.com/rate/march8" target="_blank"><strong>8 марта</strong></a>»!',
                $count, $i);

            $Reward = new ItemEkr($User, $count);
            if($Reward->give() === false) {
                throw new \Exception;
            }

            $_data = array(
                'target_login'          => 'Событие',
                'type'                  => NewDelo::TYPE_QUEST_REWARD_EKR,
                'add_info'              => '8 марта',
            );
            if($Reward->newDeloGive($_data) === false) {
                throw new \Exception;
            }

            echo sprintf('%d. %s - %s', $i, $User->login, $message).'<br>';
            if(Chat::addToChatSystem($message, $user) === false) {
                throw new \Exception;
            }

        }
    }


    public function marafonAction()
    {
        return;
        $prototypes = array();
        $temp = \components\models\Shop::whereRaw('id in (56666, 4016, 200277, 19108)')->get();
        foreach ($temp as $_item) {
            $prototypes[$_item->id] = $_item->toArray();
        }

        $chat_message = [
            1 => 'Великое чарование IV (3 шт.), Пропуск к Лорду Разрушителю (5 шт.), Средний свиток «Восстановление 720HP» (20 шт.), Большой свиток «Рунный опыт» (1 шт.)',
            2 => 'Великое чарование IV (2 шт.), Пропуск к Лорду Разрушителю (3 шт.), Средний свиток «Восстановление 720HP» (15 шт.)',
            3 => 'Великое чарование IV (1 шт.), Пропуск к Лорду Разрушителю (1 шт.), Средний свиток «Восстановление 720HP» (10 шт.)'
        ];
        $gifts = [
            1 => [
                56666 => 3,
                4016 => 5,
                200277 => 20,
                19108 => 1,
            ],
            2 => [
                56666 => 2,
                4016 => 3,
                200277 => 15
            ],
            3 => [
                56666 => 1,
                4016 => 1,
                200277 => 10,
            ]
        ];

        //$user = \components\models\User::find(7937)->toArray();

        //$rows = array($user, $user, $user);
        $db = CapitalDb::connection();
        try {

            $rows = UserQuestEvent::from('user_quest_event as uqe')
                ->join('users as u', 'u.id', '=', 'uqe.user_id')
                ->whereRaw('u.bot=0 AND u.klan!="radminion" and u.klan!="Adminion" and uqe.date_event = 2018')
                ->orderBy('rate_value', 'desc')
                ->orderBy('id', 'asc')
                ->limit(100)
                ->select(['uqe.count as rate_value', 'u.*'])
                ->get()->toArray();
            /*$rows = $db
                ->select('uqe.count as rate_value, u.*')
                ->from('user_quest_event uqe, users u')
                ->where('u.id = uqe.user_id')
                ->where('u.bot=0 AND u.klan!="radminion" and u.klan!="Adminion"')
                ->orderBy('rate_value desc, id asc')
                ->limit(100)
                ->execute()
                ->fetchAll();*/

            //$rows = [$user];

            $i = 1;
            foreach ($rows as $user) {
                $img_num = 1;
                if($i > 10 && $i < 51) {
                    $img_num = 2;
                } elseif($i > 50) {
                    $img_num = 3;
                }

                foreach ($gifts[$img_num] as $prototype_id => $count_item) {
                    if(!isset($prototypes[$prototype_id])) {
                        throw new \Exception('1');
                    }
                    $prototype = $prototypes[$prototype_id];

                    $item = ItemHelper::baseFromPrototype(array(), $prototype, array('goden' => 60));
                    $_data = array_merge($item, array(
                        'add_time'  => time(),
                        'owner'     => $user['id'],
                        'idcity'    => $user['id_city'],
                        'present'   => 'Удача',
                    ));
                    $items_ids = [];
                    for ($j = 1; $j <= $count_item; $j++) {
                        if(($item_id = Inventory::insert($_data)) === false) {
                            throw new \Exception('2');
                        }
                        $items_ids[] = $item_id;
                    }

                    $target = array(
                        'target_login' => 'Удача'
                    );
                    $info = array(
                        'add_info' => 'Марафон знаний'
                    );
                    if(\components\models\NewDelo::addDelo($user, $target, $items_ids, 307, $item, $prototype, $info) === false) {
                        throw new \Exception('3');
                    }
                }

                $message = '<font color="red">Внимание!</font> Вы получили '.$chat_message[$img_num].' за участие в событии <strong>«<a href="http://oldbk.com/encicl/?/act_sept_knowledge.html" target="_blank">Марафон знаний</a>»</strong> и за достижение '.$i.' места в <strong><a href="http://top.oldbk.com/rate/marafon" target="_blank">рейтинге события</a></strong>.';
                if(\components\models\Chat::addToChatSystem($message, $user) === false) {
                    throw new \Exception('4');
                }
                $i++;
            }

            $db->commit();
            var_dump('done');
        } catch (\Exception $ex) {
            $db->rollBack();
            VarDumper::d($ex->getMessage(), false);
            VarDumper::d($ex->getTraceAsString());
        }
    }

    public function augustAction()
    {
        return;
        $total_exp = 0;
        $total_rep = 0;
        $total_ekr = 0;

        try {
            $user_ids = [];
            $time_start = (new \DateTime('2018-08-01 00:00:00'));
            $UserQuest = UserQuest::whereRaw('is_finished = 1 and quest_id in (7,8,10,11,12,13,14,15,16,17) and created_at > ?', [
                $time_start->getTimestamp()
            ])
                ->groupBy(['user_id'])
                ->havingRaw('count(*) > ?', [4])
                ->orderBy('cnt')
                ->selectRaw('count(*) as cnt, user_id')
                ->get()->toArray();
            foreach ($UserQuest as $_item) {
                $user_ids[] = $_item['user_id'];
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            die;
        }

        /** @var User[] $Users */
        $Users = [];
        $temp = User::whereIn('id', $user_ids)->get();
        foreach ($temp as $_item) {
            $Users[$_item['id']] = $_item;
        }


        $i = 0;


        /*$Users = [546433 => User::find(546433)];
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

            $User = $Users[$_item['user_id']];

            if(QuestEventReward::whereRaw('event_id = 1 and user_id = ? and date_event = 201809', [$User->id])->count()) {
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

            $db = CapitalDb::connection();
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

                $QuestEventReward = new QuestEventReward();
                $QuestEventReward->event_id = 1;
                $QuestEventReward->name = 'Жаркий август';
                $QuestEventReward->user_id = $User->id;
                $QuestEventReward->reward = serialize(array(
                    'exp' => $exp,
                    'rep' => $rep,
                    'ekr' => $ekr
                ));
                $QuestEventReward->created_at = time();
                $QuestEventReward->date_event = 201809;
                if(!$QuestEventReward->save()) {
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

    public function august2Action()
    {
        $total_exp = 0;
        $total_rep = 0;
        $total_ekr = 0;

        try {
            $user_ids = [];
            $time_start = (new \DateTime('2018-08-01 00:00:00'));
            $UserQuest = UserQuest::whereRaw('is_finished = 1 and quest_id in (7,8,10,11,12,13,14,15,16,17) and created_at > ?', [
                $time_start->getTimestamp()
            ])
                ->groupBy(['user_id'])
                ->havingRaw('count(*) > ?', [4])
                ->orderBy('cnt')
                ->selectRaw('count(*) as cnt, user_id')
                ->get()->toArray();
            foreach ($UserQuest as $_item) {
                $user_ids[] = $_item['user_id'];
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            die;
        }

        /** @var User[] $Users */
        $Users = [];
        $temp = User::whereIn('id', $user_ids)->get();
        foreach ($temp as $_item) {
            $Users[$_item['id']] = $_item;
        }


        $i = 0;


        /*$Users = [182783 => User::find(182783)];
		$UserQuest = array(
			array(
				'user_id' => 182783,
				'cnt' => 11
			)
		);*/
        $prototype = Shop::find(541)->toArray();
        $item = ItemHelper::baseFromPrototype([], $prototype, ['goden' => 180, 'notsell' => 1, 'present' => 'Удача']);

        $j = 0;
        foreach ($UserQuest as $_item) {
            if(!isset($Users[$_item['user_id']])) {
                continue;
            }

            $User = $Users[$_item['user_id']];

            //if(QuestEventReward::whereRaw('event_id = 1 and user_id = ? and date_event = 201809', [$User->id])->count()) {
            //	continue;
            //}

            $count = (int)($_item['cnt'] / 5);

            $message = sprintf('<font color="red">Внимание!</font> Вы получили бонус за событие <b>«<a href="http://oldbk.com/encicl/?/action_august.html" target="_blank">Жаркий август</a>»</b> Сертификат на бесплатный обмен артефакта %dшт.!',
                $count);

            $j += $count;
            echo sprintf('Пользователь: %s. сертов: %d', $User->login, $count).'<br>';
            $i++;

            continue;
            $db = CapitalDb::connection();
            $db->beginTransaction();
            try {

                //репа награда
                for($c = 1; $c <= $count; $c++) {
                    $GiveItem = new ItemItem($User, $item);
                    if($GiveItem->give() === false) {
                        throw new \Exception;
                    }

                    $_data = array(
                        'target_login'          => 'Квест',
                        'type'                  => NewDelo::TYPE_QUEST_REWARD_ITEM,
                        'add_info'              => 'Жаркий август',
                    );

                    if($GiveItem->newDeloGive($_data) === false) {
                        throw new \Exception;
                    }
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

                $db->rollBack();
            } catch (\Exception $ex) {
                $db->rollBack();
                FileHelper::writeException($ex, 'august reward');
                var_dump('Error');
            }
        }

        var_dump($j);
        var_dump($i);
        var_dump($total_ekr);
        var_dump($total_exp);
        var_dump($total_rep);
    }

    public function bookAction()
    {

        return;
        $client = new Client();
        $request = $client->get('http://b.oldbk.com/api/balance');
        $response = $request->send();

        $data = json_decode($response->getBody(true), true);

        $db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            foreach ($data as $_user) {
                $User = User::where('id', '=', $_user['user_id'])->first();
                if(!$User) {
                    continue;
                }

                $ekr = floor(floatval($_user['ekr_balance']));
                if($ekr > 0) {
                    echo sprintf('%s ekr: %s', $User->login, $ekr).'<br>';
                    $GiveEkr = new ItemEkr($User, $ekr);
                    if($GiveEkr->give() === false) {
                        throw new \Exception;
                    }

                    $_data = array(
                        'target'                => 1000,
                        'target_login'          => 'Букмекер',
                        'type'                  => 1106,
                        'sum_ekr'               => $ekr,
                    );
                    if($GiveEkr->newDeloGive($_data) === false) {
                        throw new \Exception;
                    }

                    $message = sprintf('Возвращено %s екр от Букмекера', $ekr);
                    if(Chat::addToChatSystem($message, $User) === false) {
                        throw new \Exception;
                    }
                }

                $gold = floor(floatval($_user['gold_balance']));
                if($gold > 0) {
                    echo sprintf('%s gold: %s', $User->login, $gold).'<br>';

                    $GiveGold = new ItemGold($User, $gold);
                    if($GiveGold->give() === false) {
                        throw new \Exception;
                    }

                    $_data = array(
                        'target'                => 1000,
                        'target_login'          => 'Букмекер',
                        'type'                  => 1351,
                        'sum_kr'                => $gold,
                    );
                    if($GiveGold->newDeloGive($_data) === false) {
                        throw new \Exception;
                    }

                    $message = sprintf('Возвращено %s монет от Букмекера', $gold);
                    if(Chat::addToChatSystem($message, $User) === false) {
                        throw new \Exception;
                    }
                }
            }

            $db->commit();

            var_dump('done');
        } catch (\Exception $ex) {
            $db->rollback();
            echo '<pre>';
            var_dump('error');
            var_dump($ex->getMessage());
            var_dump($ex->getTraceAsString());
        }
    }
}
