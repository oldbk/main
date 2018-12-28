<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 01.06.2016
 */

namespace components\Helper;


use components\Component\Db\CapitalDb;
use components\Component\VarDumper;
use components\Helper\item\ItemRep;
use components\models\Chat;
use components\models\Inventory;
use components\models\NewDelo;
use components\models\Shop;
use components\models\TopRate;
use components\models\User;
use components\models\UserBadge;

class BadgeGiveHelper
{
	protected static $_user_ids_ignore = [
		95673, 53373, 630645, 540875, 317377, 689608, 47918, 294342,
		339510, 14797, 58138, 262023
	];

    public static function may()
    {
        $ended = new \DateTime('2019-06-01 00:00:00');

        $prototypes = array();
        $temp = Shop::whereIn('id', [2016004, 2016005, 2016006])->get()->toArray();
        //$temp = Shop::findAll('id in (2016004, 2016005, 2016006)')->asArray();
        foreach ($temp as $_item) {
            $prototypes[$_item['id']] = $_item;
        }

		$db = CapitalDb::connection();
		$db->beginTransaction();
        try {
			$TopSend = User::from('users as u')
				->whereRaw('u.bot=0 AND u.klan!="radminion" and u.klan!="Adminion"')
				->whereIn('u.id', self::$_user_ids_ignore, 'and', true)
				->orderBy('rate_value', 'desc')
				->orderBy('u.id', 'asc')
				->limit(100)
				->select(['u.*', 'u.znak as rate_value'])
				->get()->toArray();

            //$user_test = User::findByPk(684792)->asArray();
            $i = 1;
            $rate_image = 'http://i.oldbk.com/i/i/item2016_signhero_%d.png';

            echo '<pre>';
            foreach ($TopSend as $user) {
                $img_num = 1;
                if($i > 10 && $i < 51) {
                    $img_num = 2;
                } elseif($i > 50) {
                    $img_num = 3;
                }
                $img = sprintf($rate_image, $img_num);

                switch ($img_num) {
                    case 1:
                        $alt = 'Золотая медаль «Победный май»';
                        $prototype_id = 2016004;
                        break;
                    case 2:
                        $alt = 'Серебряная медаль «Победный май»';
                        $prototype_id = 2016005;
                        break;
                    default:
                        $alt = 'Бронзовая медаль «Победный май»';
                        $prototype_id = 2016006;
                        break;
                }

                if(!isset($prototypes[$prototype_id])) {
                    throw new \Exception;
                }

                $datetime = new \DateTime();
                $datetime->modify('+90 day');

                $prototype = $prototypes[$prototype_id];
                $item = ItemHelper::baseFromPrototype(array(), $prototype);
                $_data = array_merge($item, array(
                    'add_time'  => time(),
                    'owner'     => $user['id'],
                    'sowner'    => $user['id'],
                    'idcity'    => $user['id_city'],
                    'present'   => 'Администрация ОлдБК',
                    'getfrom'   => 200,
                    'goden'     => 90,
                    'dategoden' => $datetime->getTimestamp(),
                ));
                if(($item_id = Inventory::insert($_data)) === false) {
                    throw new \Exception;
                }

                echo $i.'. '.$user['login']. ' - '.$item['name'].PHP_EOL;

                $target = array(
                    'target_login' => 'Удача'
                );
                $info = array(
                    'add_info' => 'Победный май'
                );
                if(NewDelo::addDelo($user, $target, array($item_id), 307, $item, $prototype, $info) === false) {
                    throw new \Exception;
                }

                $r = UserBadge::addOrUpdateExpire(
                    $user['id'],
                    $img,
                    $alt,
                    $ended->getTimestamp(),
                    UserBadge::TYPE_MAY,
                    'http://top.oldbk.com/rate/may'
                );
                if($r === false) {
                    throw new \Exception;
                }

                $message = '<font color="red">Внимание!</font> Вы получили <strong>'.$item['name'].'</strong> за участие в рейтинге «<a href="http://top.oldbk.com/rate/may" target="_blank">Победный май</a>»';
                if(Chat::addToChatSystem($message, $user) === false) {
                    throw new \Exception;
                }
                $i++;
            }

            $db->commit();

            var_dump('done');
        } catch (\Exception $ex) {
            $db->rollBack();

            var_dump($ex->getMessage());
            var_dump($ex->getTraceAsString());
            var_dump('error');
        }
    }

    public static function maslo()
    {
    	return;
		$db = CapitalDb::connection();

        $ended = new \DateTime();
        $ended->modify('+1 year');

		$get_rows = TopRate::from('top_rate as tr')
			->join('users as u', 'u.id', '=', 'tr.user_id')
			->whereRaw('u.bot=0 AND u.klan!="radminion" and u.klan!="Adminion"')
			->whereRaw('tr.action_type=? and tr.rate_type=?', [UserBadge::TYPE_MASLO.'2018', 2])
			->orderBy('rate_value', 'desc')
			->orderBy('updated_at', 'desc')
			->limit(100)
			->select(['tr.value as rate_value', 'u.*'])
			->get()->toArray();
		//$get_rows = [];

		$give_rows = TopRate::from('top_rate as tr')
			->join('users as u', 'u.id', '=', 'tr.user_id')
			->whereRaw('u.bot=0 AND u.klan!="radminion" and u.klan!="Adminion"')
			->whereRaw('tr.action_type=? and tr.rate_type=?', [UserBadge::TYPE_MASLO.'2018', 1])
			->orderBy('rate_value', 'desc')
			->orderBy('updated_at', 'desc')
			->limit(100)
			->select(['tr.value as rate_value', 'u.*'])
			->get()->toArray();
		//$give_rows = [];

		//VarDumper::d(count($get_rows), false);
		//VarDumper::d(count($give_rows), false);

		//$_user = User::find(546433)->toArray();
		//$_user['rate_value'] = 100;

		$db->beginTransaction();
        try {

			self::giveMaslo($give_rows, $ended, 'Топ Дарители. %d место', 1);
			self::giveMaslo($get_rows, $ended, 'Топ Популярности. %d место', 2);


            $db->commit();

            var_dump('done');
        } catch (\Exception $ex) {
            $db->rollBack();

            var_dump($ex->getMessage());
            var_dump($ex->getTraceAsString());
            var_dump('error');
        }
    }

    public static function masloTest()
    {
        return;
        $ended = new \DateTime();
        $ended->modify('+1 year');

        $user = User::findByPk(546433)->asArray();
        $user['rate_value'] = 100;

        $db = TopRate::model()->db();
        $db->beginTransaction();
        try {
            self::giveMaslo(array($user), $ended, 'Топ Дарители. %d место');


            $db->commit();

            var_dump('done');
        } catch (\Exception $ex) {
            $db->rollBack();

            var_dump($ex->getMessage());
            var_dump($ex->getTraceAsString());
            var_dump('error');
        }
    }

    /**
     * @param $List
     * @param \DateTime $ended
     * @param $alt
     * @param $type
     * @throws \Exception
     */
    private static function giveMaslo($List, $ended, $alt, $type)
    {
        $i = 0;
        $rate_image = 'http://i.oldbk.com/i/i/masl2016_%d_%s.png';
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
			$rep_count = (int)($user['rate_value'] / 2);
			
            /*$r = UserBadge::addOrUpdateExpire(
                $user['id'],
                $img,
                sprintf($alt, $i),
                $ended->getTimestamp(),
                UserBadge::TYPE_MASLO.'_'.$type,
                'http://top.oldbk.com/rate/maslo'
            );
            if($r === false) {
                throw new \Exception;
            }


            $User = User::find($user['id']);
            $Repa = new ItemRep($User, $rep_count);
            if($Repa->give() === false) {
                throw new \Exception;
            }

            $_data = array(
                'target_login'          => 'Событие',
                'type'                  => NewDelo::TYPE_QUEST_REWARD_REP,
                'add_info'              => 'Масленица 2018',
            );
            if($Repa->newDeloGive($_data) === false) {
                throw new \Exception;
            }*/

            $message = '<font color="red">Поздравляем!</font> Вы получили <strong>'.$rep_count.'</strong> репутации за участие в рейтинге события «<a href="http://top.oldbk.com/rate/maslo" target="_blank">Масленица</a>»';
            //VarDumper::d($message, false);
            if(Chat::addToChatSystem($message, $user) === false) {
                throw new \Exception;
            }
        }
    }
}