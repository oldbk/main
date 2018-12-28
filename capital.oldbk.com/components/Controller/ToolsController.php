<?php
namespace components\Controller;
use components\Component\Db\CapitalDb;
use components\Component\Loto;
use components\Component\VarDumper;
use components\Controller\_base\AdminController;
use components\Helper\BadgeGiveHelper;
use components\Helper\ItemHelper;
use components\Helper\Json;
use components\models\Bank;
use components\models\BankHistory;
use components\models\Chat;
use components\models\ConfigKoSettings;
use components\models\Effect;
use components\models\effect\Travma;
use components\models\Eshop;
use components\models\Inventory;
use components\models\ItemLoto;
use components\models\ItemLotoRas;
use components\models\NewDelo;
use components\models\Settings;
use components\models\User;
use components\models\UserBadge;
use components\models\UserZnahar;
use okw\CF\CF;
use okw\CF\Exception\CFException;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 *
 */
class ToolsController extends AdminController
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

    public function indexAction()
    {
        $this->render('index', array('user' => $this->user));
    }

    public function invalidateAction()
    {
        $cf_api_client = new CF("admin@oldbk.com", "7feac20f10e2a2d1fe76");

        $response = array();
        try {
            $response = $cf_api_client->zone_file_purge(array(
                'z' => $this->app->request->post('p1'),
                'url' => $this->app->request->post('p2'),
            ));
        } catch (CFException $ex) {

        }

        if(isset($response['result']) && $response['result'] == 'success') {
            $this->app->flash('success', sprintf('Инвалидировали файл %s', $response['response']['url']));
        } else {
            $this->app->flash('success', sprintf('Ошибка инвалидации %s', isset($response['msg']) ? $response['msg'] : ''));
        }

        $this->redirect($this->app->urlFor('tools', array('action' => 'index')));
    }

    public function badgeAction()
    {
        BadgeGiveHelper::may();
    }

    public $LotoItemList;
    public $LotoItemListStock;
    public function loto_simulationAction()
    {
        $LotoInfo = ItemLotoRas::whereRaw('status = 1')->first();
        if(!$LotoInfo) {
            return false;
        }
		$LotoInfo = $LotoInfo->toArray();

        $Loto = new Loto\Loto($LotoInfo['id']);
        //$Loto->setDebug();

        $owner_ids = array();
        $TicketList = array();
        $TicketArray = ItemLoto::whereRaw('loto = ?', [$Loto->getLotoId()])->orderBy('id', 'desc')->get(['id', 'owner'])->toArray();
        foreach ($TicketArray as $Ticket) {
            if(!in_array($Ticket['owner'], $owner_ids)) {
                $owner_ids[] = $Ticket['owner'];
            }
            $TicketList[$Ticket['id']] = $Ticket['owner'];
        }
        unset($TicketArray);

        $owner_array = array();
        $OwnerList = User::whereIn('id', $owner_ids)->get(['id', 'id_city', 'money', 'login'])->toArray();
        foreach ($OwnerList as $Owner) {
            $owner_array[$Owner['id']] = $Owner;
        }

        $response = array();

        $db = CapitalDb::connection();
        $keys = array_keys($TicketList);
        shuffle($keys);
        shuffle($keys);
        foreach ($keys as $ticket_id) {
            $owner_id = $TicketList[$ticket_id];
            $db->beginTransaction();
            try {
                $ItemOject =& $Loto->getItem();
                $item = $ItemOject->getItem();
                $Owner = $owner_array[$owner_id];

                $response[$ticket_id] = array(
                    'bilet'         => $ticket_id,
                    'user_login'    => $Owner['login'],
                    'item_name'     => $item['name'],
                );

                $db->rollBack();
            } catch (\Exception $ex) {
                $db->rollBack();
                VarDumper::dump($ex->getMessage());
                VarDumper::dump($ex->getTraceAsString());
            }
        }

        ksort($response);
        echo Json::encode($response);
    }

    public function timeAction()
    {
        $date_string = $this->app->request->get('date');
        if(!$date_string) {
            VarDumper::dump('Некорректная дата');die;
        }

        $date = new \DateTime($date_string);
        VarDumper::dump($date->getTimestamp());
    }

    public function lotoAction()
    {
    	/** @var ItemLotoRas $LotoInfo */
		$LotoInfo = ItemLotoRas::where('status', '=', 1)->first();
		if(!$LotoInfo) {
			$this->renderJSON(array(
				'error' => true,
				'message' => 'Не нашли тираж'
			));
		}

        if($LotoInfo->lotodate > time()) {
            $this->renderJSON(array());
        }
        if($LotoInfo->in_process == 1) {
            $this->renderJSON(array(
                'error' => true,
                'message' => 'Еще в процессе'
            ));
        }
		$LotoInfo->in_process = 1;
		$LotoInfo->save();

        $Loto = new Loto\Loto($LotoInfo['id']);
        //$Loto->setDebug();

        $owner_ids = array();
        $TicketList = array();
        $TicketArray = ItemLoto::where('loto', '=', $Loto->getLotoId())
			->orderBy('id', 'desc')
			->get(['id', 'owner'])->toArray();
        foreach ($TicketArray as $Ticket) {
            if(!in_array($Ticket['owner'], $owner_ids)) {
                $owner_ids[] = $Ticket['owner'];
            }
            $TicketList[$Ticket['id']] = $Ticket['owner'];
        }
        unset($TicketArray);

        $owner_array = array();
        $OwnerList = User::whereIn('id', $owner_ids)->get(['id', 'id_city', 'money', 'login'])->toArray();
        foreach ($OwnerList as $Owner) {
            $owner_array[$Owner['id']] = $Owner;
        }

        $keys = array_keys($TicketList);
        shuffle($keys);
        shuffle($keys);

        $db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            foreach ($keys as $ticket_id) {
                $owner_id = $TicketList[$ticket_id];
                $Owner = $owner_array[$owner_id];

                //выдаем предмет
                if(!$Loto->give($Owner, $ticket_id)) {
                    throw new \Exception();
                }
            }

            if(!$Loto->finish()) {
                throw new \Exception();
            }

            $db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();
			$LotoInfo->in_process = 0;
			$LotoInfo->save();

            $this->renderJSON(array(
                'error'     => true,
                'message'   => $ex->getMessage(),
                'code'      => $ex->getCode(),
                'trace'     => $ex->getTraceAsString(),
            ));
        }

        //Выдаем бейджи
        $badge = true;
        try {
            $DateTime = new \DateTime();
            $DateTime->modify('+1 week')
                ->setTime(20,0);
            $Loto = new Loto\LotoView($Loto->getLotoId() + 1);
            foreach ($Loto->getLastWin() as $user) {
                UserBadge::addOrUpdateExpire(
                    $user['user']->id,
                    'http://i.oldbk.com/i/badge/loto.png',
                    'Участник топ-100 победителей '.($Loto->getLotoId() - 1).' розыгрыша Лотереи ОлдБК',
                    $DateTime->getTimestamp(),
                    UserBadge::TYPE_LOTO,
                    'http://top.oldbk.com/rate/loto'
                );
            }
        } catch (\Exception $ex) {
            $badge = false;
        }
        if($badge === false) {
            $this->renderJSON(array(
                'error'     => true,
                'message'   => 'Проблема с бейджами',
            ));
        }

        $this->renderJSON(array(
            'success' => true,
            'message' => 'Лото завершилось без ошибок',
        ));
    }

    public function loto_messageAction()
    {
        $Loto = new Loto\LotoMessage();
        if($Loto->sendMessages() === false) {
            $this->renderJSON(array(
                'success' => false,
                'message' => 'Неудалось отправить сообщения в лото',
            ));
        }

        $this->renderJSON(array());
    }

    public function lotofixAction()
    {
        $Loto = new Loto\LotoMessage();
        $Loto->sendMessages();
    }

    public function lotogiveAction()
    {
        echo '<pre>';
        $db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            $User = User::find(546433)->toArray();

            $Loto = new Loto\Loto(178);
            $Loto->setDebug();
            foreach ($Loto->getItemCountById() as $loto_item_id => $count) {
                if(!$Loto->give($User, null, $loto_item_id)) {
                    throw new \Exception();
                }
            }

            $db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();
            VarDumper::dump($ex->getMessage());
            VarDumper::dump($ex->getTraceAsString());
        }

        var_dump('done');
    }

    public function fixnelechAction()
    {
        $users = array(
            'Отдушина',
            'Консерватор',
            'Дядя Саша',
            'Имам Шамиль',
            'Зловещий',
            'Стройный',
            'Zloj Tapok',
            'тетьНюра',
            'gwindor',
            'warriordead',
            //'Безликий Ангел',
            'felo-de-se',
            'Korshun',
            'Совершенство',
            'Pro100Olea'
        );

        $id = $this->app->request->get('user_id');
        if(!$id) {
            //throw new \Exception();
        }

        $db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            foreach ($users as $_user) {
                $User = User::where('login', '=', $_user)->first();
                if(!$User) {
                    throw new \Exception('User not found. '.$_user);
                }
				$User = $User->toArray();

                $count = Effect::whereRaw('type = 14 and owner = ? and sila = 50', [$User['id']])->count();
                if(!$count) {
                    continue;
                }

                $_data = array(
                    'sila' => $User['sila'] + 50,
                    'lovk' => $User['sila'] + 50,
                    'inta' => $User['sila'] + 50,
                );
                User::where('id', '=', $User['id'])->update($_data);

                VarDumper::d($User['login'], false);
            }

            $db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();
            var_dump('error');
            VarDumper::d($ex->getTraceAsString());
        }

        var_dump('done');
    }

    public function nelechAction()
    {
        $id = $this->app->request->get('user_id');
        if(!$id) {
            throw new \Exception();
        }

		$db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            $User = User::find($id)->toArray();
            if(!$User) {
                throw new \Exception('User not found. '.$id);
            }

            $_stats = array(
                'sila' => 50,
                'lovk' => 50,
                'inta' => 50,
                //'vinos' => 25,
            );

            $datetime = new \DateTime();
            $datetime->modify('+1 year');
            if(!Travma::nelech($User['id'], $datetime, $_stats)) {
                throw new \Exception();
            }

            $db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();
            var_dump('error');
            VarDumper::d($ex->getTraceAsString());
        }

        var_dump('done');
    }

    public function marafonAction()
    {
        return;
        echo '<pre>';
        $prototypes = array();
        $temp = Eshop::whereIn('id', [33053, 33052, 33054])->get()->toArray();
        foreach ($temp as $_item) {
            $prototypes[$_item['id']] = $_item;
        }

        //$user = User::findByPk(546433)->asArray();

		$db = CapitalDb::connection();
        $rows = array();
        //$rows = array($user, $user, $user);
        try {
            $db->beginTransaction();
            $rows = $db
                ->select('uqe.count as rate_value, u.*')
                ->from('user_quest_event uqe, users u')
                ->where('u.id = uqe.user_id')
                ->where('u.bot=0 AND u.klan!="radminion" and u.klan!="Adminion"')
                ->orderBy('rate_value desc, id asc')
                ->limit(100)
                ->execute()
                ->fetchAll();


            $i = 1;
            foreach ($rows as $user) {
                $img_num = 1;
                if($i > 10 && $i < 51) {
                    $img_num = 2;
                } elseif($i > 50) {
                    $img_num = 3;
                }

                $link = '';
                switch ($img_num) {
                    case 1:
                        $prototype_id = 33053;
                        $link = 'http://oldbk.com/encicl/?/eda/dinner_dragon2.html';
                        break;
                    case 2:
                        $link = 'http://oldbk.com/encicl/?/eda/dinner_dragon1.html';
                        $prototype_id = 33052;
                        break;
                    default:
                        $link = 'http://oldbk.com/encicl/?/eda/dinner_viking1.html';
                        $prototype_id = 33054;
                        break;
                }

                if(!isset($prototypes[$prototype_id])) {
                    throw new \Exception;
                }

                $prototype = $prototypes[$prototype_id];
                $item = ItemHelper::baseFromPrototype(array(), $prototype, array('goden' => 30));
                $_data = array_merge($item, array(
                    'add_time'  => time(),
                    'owner'     => $user['id'],
                    'idcity'    => $user['id_city'],
                    'present'   => 'Администрация ОлдБК',
                ));
                if(($item_id = CapitalDb::table(Inventory::tableName())->insertGetId($_data))) {
                    throw new \Exception;
                }

                var_dump($user['login']. ' - '.$item['name'].' ('.$item_id.')');

                $target = array(
                    'target_login' => 'Удача'
                );
                $info = array(
                    'add_info' => 'Марафон знаний'
                );
                if(NewDelo::addDelo($user, $target, array($item_id), 307, $item, $prototype, $info) === false) {
                    throw new \Exception;
                }

                $message = '<font color="red">Внимание!</font> Вы получили <strong><a href="'.$link.'" target="_blank">'.$item['name'].'</a></strong> за участие в событии <strong>«<a href="http://oldbk.com/encicl/?/act_sept_knowledge.html" target="_blank">Марафон знаний</a>»</strong> и достижении '.$i.' места в <strong><a href="http://top.oldbk.com/rate/marafon" target="_blank">рейтинге события</a></strong>.';
                if(Chat::addToChatSystem($message, $user) === false) {
                    throw new \Exception;
                }
                $i++;
            }

            $db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();
        }
    }

    public function checkConfigAction()
	{
		$d = $this->app->cache->get('configKo2', ['all_keys' => true]);
		//$d = $this->app->cache->get('configKo2');
		unset($d['value']);

		/*$builder = ConfigKoSettings::from('config_ko_settings as cks')
			->join('config_ko_main as ckm', 'ckm.id', '=', 'cks.main_id')
			->where('ckm.is_enabled', '=', 1)
			->select(['cks.*']);
		$config = $builder->get()->toArray();
		$this->app->cache->set('configKo2', $config);*/

		VarDumper::d($d, false);
		var_dump($this->app->cache->isExisting('configKo2'));
	}

	public function znaharAction()
	{
		try {
			CapitalDb::table(UserZnahar::tableName())->update(['klass' => 1]);
			echo 'Finish'.PHP_EOL;
		} catch (\Exception $ex) {
			echo 'Error. See log file cron_znahar_daily.txt'.PHP_EOL;
			\components\Helper\FileHelper::writeException($ex, 'cron_znahar_daily');
		}
	}
}