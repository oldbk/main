<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 02.06.2016
 */

namespace components\Controller;


use components\Component\Db\CapitalDb;
use components\Component\Quests\Quest;
use components\Component\VarDumper;
use components\Controller\_base\BaseController;
use components\Helper\FileHelper;
use components\Helper\PluginViolation;
use components\models\ConfigKoSettings;
use components\models\UserAbils;
use components\models\ApiClient;
use components\models\Clan;
use components\models\ClanWarNew;
use components\models\Effect;
use components\models\Inventory;
use components\models\quest\QuestList;
use components\models\RuinesItems;
use components\models\RuinesMap;
use components\models\User;
use components\models\UserComplect;
use components\models\PluginAnalyze;
use components\models\PluginUserWarning;
use Guzzle\Http\Client;



class ApiController extends BaseController
{
    private $_salt = 'S4rr84taJfeXVWBSQ3Zj';
	private $_cache_time = 10;

    public function scriptAction()
    {
        if($this->app->webUser->isGuest()) {
            $this->renderJSON(array(
                'error' => 'invalidated hash',
            ));
        }
        $userId = $this->app->webUser->getId();
        $userLogin = $this->app->webUser->getLogin();

        $_hash_ = $this->app->request->post('hash');
        $scriptsSrc = $this->app->request->post('scriptSrc', array());
        if(!is_array($scriptsSrc)) {
            $scriptsSrc = array($scriptsSrc);
        }
        $scriptsCode = $this->app->request->post('scriptCode', array());
        if(!is_array($scriptsCode)) {
            $scriptsCode = array($scriptsCode);
        }

        $_hashTime = $_SESSION['_hash_time_'];
        $_hashCreate = md5($userId.'- '.md5($userLogin).md5($_hashTime));

        if($_hash_ != $_SESSION['_hash_']) {
            //@TODO
            $this->renderJSON(array(
                'error' => 'invalidated hash',
            ));
        }

        $errors = array('invalidate hash', 'too many connection', 'error try later');
        $key = rand(0, 2);
        $response = array(
            'error' => $errors[$key]
        );


        if(($found = $this->checkScripts($scriptsSrc)) !== true || ($found = $this->checkCodes($scriptsCode)) !== true) {
            try {
                $PluginUser = PluginUserWarning::whereRaw('user_id = ?', [$userId])->first();
                if(!$PluginUser) {
                    $PluginUser = new PluginUserWarning();
                    $PluginUser->user_id = $userId;
                    $PluginUser->count = 0;
                    $PluginUser->data = serialize(array($found));
                } elseif(time() < $PluginUser->finish_interval) {
                    throw new \Exception();
                }

                $_data = unserialize($PluginUser->data);
                if(!in_array($found, $_data)) {
                    $_data[] = $found;
                }

                $intervalDateTime = new \DateTime();
                $intervalDateTime->modify('+'.(rand(30, 90)).' minutes');

                $PluginUser->login = $this->app->webUser->getLogin();
                $PluginUser->data = serialize($_data);
                $PluginUser->count++;
                $PluginUser->updated_at = time();
                $PluginUser->finish_interval = $intervalDateTime->getTimestamp();
                if(!$PluginUser->save()) {
                    throw new \Exception('error add user '. $userId, 999);
                }

                $db = CapitalDb::connection();
				$db->beginTransaction();
                try {
                    $PluginViolation = new PluginViolation($this->app->webUser->getUser());
                    if(!$PluginViolation->make($PluginUser->count)) {
                        throw new \Exception('Не смогли повесить наказание. Пользователь: %s. Номер наказания: %d', $this->app->webUser->getLogin(), $PluginUser->count);
                    }

					$db->commit();

                    if($msg = $PluginViolation->getMessage()) {
                        $response = array(
                            'message' => $this->renderPartial('message', array('message' => $PluginViolation->getMessage()), true),
                        );
                    }

                } catch (\Exception $ex) {
					$db->rollBack();
                    FileHelper::writeException($ex, 'api_log');
                }
            } catch (\Exception $ex) {
                FileHelper::writeException($ex, 'api_log');
            }
        }

        $this->renderJSON($response);
    }

    protected function checkScripts($scripts)
    {
        $data = array();

		$db = CapitalDb::connection();
		$db->beginTransaction();
        try {
            $allowed = array();
            $disallow = array();
            $List = PluginAnalyze::whereRaw('src is not null')->get()->toArray();
            foreach ($List as $_item) {
                if($_item['is_correct'] == 1) {
                    $allowed[] = $_item['src'];
                } else {
                    $disallow[] = $_item['src'];
                }
            }

            $updatedSrc = array();
            foreach ($scripts as $script) {
                $host = parse_url($script, PHP_URL_HOST);
                if(in_array($host, $allowed)) {
                    continue;
                }

                if(in_array($host, $disallow)) {
					$db->commit();
                    return $script;
                }

                if(!isset($data[$host])) {
                    $data[$host] = array();
                }
                if(!in_array($script, $data[$host])) {
                    $data[$host][] = $script;
                }

                if(!in_array($host, $updatedSrc)) {
                    $updatedSrc[] = $host;
                }
            }
            foreach ($updatedSrc as $_item) {
                $_data = array(
                    'src' => $_item,
                    'created_at' => time(),
                    'data' => serialize(isset($data[$_item]) ? $data[$_item] : array()),
                );
                PluginAnalyze::insert($_data);
            }

			$db->commit();
        } catch (\Exception $ex) {
			$db->rollBack();

            FileHelper::writeException($ex, 'api_script');
        }

        return true;
    }

    protected function checkCodes($codes)
    {
		$db = CapitalDb::connection();
		$db->beginTransaction();
        try {
            $allowed = array();
            $disallow = array();
            $List = PluginAnalyze::whereRaw('code is not null')->get()->toArray();
            foreach ($List as $_item) {
                if($_item['is_correct']) {
                    $allowed[$_item['check_param']] = $_item['code'];
                } else {
                    $disallow[$_item['check_param']] = $_item['code'];
                }
            }

            $updatedCode = array();
            foreach ($codes as $script) {
                if(preg_match('/(function auto)|(document.domain = .oldbk.com.)|(window.CloudFlare)|(location.href=.plrfr.php.)|(a75def781b)|(swiffy.Stage)|(var progressEnd = 40)|(var progressInterval = 100)|(top.slid)|(refreshPeriodic)/ui', $script)) {
                    continue;
                }

                $script = trim($script);

                $md5 = md5($script);
                if(key_exists($md5, $allowed) || in_array($script, $allowed)) {
                    continue;
                }

                if(key_exists($md5, $disallow) || in_array($script, $disallow)) {
                    $db->commit();
                    return $md5;
                }

                $updatedCode[$md5] = $script;
            }
            foreach ($updatedCode as $md5 => $_item) {
                $_data = array(
                    'code' => $_item,
                    'check_param' => $md5,
                    'created_at' => time(),
                );
                PluginAnalyze::insert($_data);
            }

			$db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();

            FileHelper::writeException($ex, 'api_script');
        }

        return true;
    }

    public function generateAction()
    {
        if($this->app->webUser->isGuest()) {
            throw new \Exception('Page not found');
        }

        $userId = $this->app->webUser->getId();

        $client = new Client();
        $request = $client->post('http://plug.oldbk.com/generate.php', null, array(
            'user_id' => $userId,
            'ips' => array(
                'X_FORWARDED_FOR'       => isset($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : '',
                'HTTP_X_FORWARDED_FOR'  => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '',
                'CLIENT_IP'             => isset($_SERVER['CLIENT_IP']) ? $_SERVER['CLIENT_IP'] : '',
                'REMOTE_ADDR'           => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
                'HTTP_CF_CONNECTING_IP' => isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : '',
            ),
        ));
        $response = $request->send();

        $data = json_decode($response->getBody(true), true);

        $this->renderJSON(array(
            'link' => $data['link'],
            'code' => $data['code'],
        ));
    }

    public function checkAction()
    {
        if($this->app->webUser->isGuest()) {
            throw new \Exception('Page not found');
        }
        $code1 = $this->app->request->post('code1');
        $code2 = $this->app->request->post('code2');

		$db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            $PluginUser = PluginUserWarning::whereRaw('user_id = ?', [$this->app->webUser->getId()])->first();
            if(!$PluginUser) {
                $PluginUser = new PluginUserWarning();
                $PluginUser->user_id = $this->app->webUser->getId();
                $PluginUser->login = $this->app->webUser->getLogin();
                $PluginUser->data = serialize(array());
                $PluginUser->change_host_count = 0;
                $PluginUser->total_check_host = 0;
            }

            if($code1 != $code2) {
                if($PluginUser->change_host == 0) {
                    $PluginUser->total_check_host = 0;
                    $PluginUser->change_host_count = 0;
                }

                $PluginUser->change_host = 1;
                $PluginUser->change_host_count++;
            }

            $PluginUser->total_check_host++;
            $PluginUser->updated_at = time();
            if(!$PluginUser->save()) {
                throw new \Exception('error add user '. $this->app->webUser->getId(), 999);
            }

            $db->commit();
        } catch (\Exception $ex) {
            $db->rollBack();

            FileHelper::writeException($ex, 'api_script');
        }

        $this->renderJSON(array());
    }

    public function loginAction()
    {
        $response = array();

        $api_key = $this->app->request->get('api_key');
        try {
            if(!$api_key) {
                throw new \Exception('Invalid api key');
            }
            /** @var ApiClient $ApiClient */
            $ApiClient = ApiClient::whereRaw('api_key = ?', [$api_key])->get()->first();
            if(!$ApiClient) {
                throw new \Exception('Invalid api key');
            }
			$ApiClient = $ApiClient->toArray();
            if($ApiClient['_token_expire'] < time()) {
                $_token = ApiClient::generateToken();

                $expire = new \DateTime();
                $expire->modify('+1 day');
                $_data = array(
                    '_token' => $_token,
                    '_token_expire' => $expire->getTimestamp(),
                    'updated_at' => time(),
                );
                if(!ApiClient::whereRaw('id = ?', [$ApiClient['id']])->update($_data)) {
                    throw new \Exception('Try again later');
                }
            } else {
                $_token = $ApiClient['_token'];
                $expire = new \DateTime();
                $expire->setTimestamp($ApiClient['_token_expire']);
            }

            $response = array(
                'success' => true,
                '_token' => $_token,
                'expire' => $expire->getTimestamp()
            );

        } catch (\Exception $ex) {
            $response = array(
                'error' => true,
                'message' => $ex->getMessage(),
            );
        }

        $this->renderJSON($response);
    }

    private function decode($api_key, $token, $time)
    {
        $DateTime = new \DateTime();
        $DateTime->modify('-5 min');

        $DateTime1 = new \DateTime();
        $DateTime1->modify('+1 min');
        if($time < $DateTime->getTimestamp() || $time > $DateTime1->getTimestamp()) {
            return false;
        }

        $md5 = md5($api_key . $token . $time);
        $hash = base64_encode($md5 . $this->_salt);

        return array(
            'api_key'   => $api_key,
            'token'     => $token,
            'time'      => $time,
            'salt'      => $this->_salt,
            'md5'       => $md5,
            'hash'      => $hash
        );
    }

    private function auth()
    {
        $token = $this->app->request->get('token');


        if(!$token) {
            throw new \Exception('Invalid token');
        }

        $ApiClient = ApiClient::whereRaw('_token = ? ', [$token])->first()->toArray();

        if(!$ApiClient) {
            return false;
        }

        return $ApiClient;
    }

	public function playerAction()
	{
		$response = array();
		$hash = null;
		try {
			if($this->app->webUser->isGuest()) {
				throw new \Exception('User not found');
			}
			$user_id = $this->app->webUser->getId();

			/** @var User $User */
			$User = User::find($user_id);
			if(!$User) {
				throw new \Exception('Invalid USER');
			}

			$data = array(
				'date' => time(),
				'player' => array(
					'id'                => (int)$User['id'],
					'align'				=> $User['align'],
					'login'             => $User['login'],
					'level'             => (int)$User['level'],
					'clan'              => $User['klan'],
					'clanstatus'        => $User['status'],
					'hp'                => (int)$User['hp'],
					'hpfull'            => (int)$User['maxhp'],
					'mp'                => (int)$User['mana'],
					'mpfull'            => (int)$User['maxmana'],
					'exp'               => (int)$User['exp'],
					'expup'             => (int)$User['nextup'],
					'battle_id'         => (int)$User['battle'],
					'war'               => 0,
					'inventorysets'     => array(),
					'playerbuffs'       => array(),
					'playerabils'   	=> array(),
					'gamepaidstatus'    => array(
						'type' => 'none',
						'date' => 0
					),
					'clientpaidstatus'  => array(
						'type' => 'standart',
						'date' => 0
					),
					'slots' 			=> array(
						'sergi'		=> $User['sergi'] > 0 ? $User['sergi'] : false,
						'kulon'		=> $User['kulon'] > 0 ? $User['kulon'] : false,
						'perchi' 	=> $User['perchi'] > 0 ? $User['perchi'] : false,
						'weapon'	=> $User['weap'] > 0 ? $User['weap'] : false,
						'armor'		=> $User['bron'] > 0 ? $User['bron'] : false,
						'helm'		=> $User['helm'] > 0 ? $User['helm'] : false,
						'shit'		=> $User['shit'] > 0 ? $User['shit'] : false,
						'boots'		=> $User['boots'] > 0 ? $User['boots'] : false,
						'r1'		=> $User['r1'] > 0 ? $User['r1'] : false,
						'r2'		=> $User['r2'] > 0 ? $User['r2'] : false,
						'r3'		=> $User['r3'] > 0 ? $User['r3'] : false,
					),
					'stats'				=> array(
						'sila' 	=> $User['sila'],
						'lovk' 	=> $User['lovk'],
						'inta' 	=> $User['inta'],
						'vinos' => $User['vinos'],
						'intel' => $User['intel'],
						'mudra' => $User['mudra'],
					),
				),
			);

			//get clan war
			if($User['klan']) {
				/** @var Clan $Clan */
				$Clan = Clan::where('short', '=', $User['klan'])->first(['id']);
				if($Clan) {
					$ClanWar = ClanWarNew::whereRaw('agressor = ? or defender = ?', [$Clan['id'], $Clan['id']])->first(['id']);
					if($ClanWar) {
						$data['player']['war'] = (int)$ClanWar['id'];
					}
				}
			}

			//get complect
			$Complect = UserComplect::whereRaw('owner = ?', [$User['id']])->get(['id','name']);
			foreach ($Complect as $_item) {
				$data['player']['inventorysets'][] = array(
					'id'    => (int)$_item['id'],
					'name'  => $_item['name']
				);
			}

			//get baffs
			$Effects = Effect::whereRaw('owner = ? and type not in (4999, 5999, 6999) and name != ""', [$User['id']])->get(['name','time','type']);
			foreach ($Effects as $Effect) {
				$data['player']['playerbuffs'][] = array(
					'name'  => $Effect['name'],
					'date'  => (int)$Effect['time'],
					'id'	=> $Effect['type'],
				);
			}

			//get abils
			$Abils = UserAbils::whereRaw('owner = ?', [$User['id']])->get(['magic_id','allcount','findata','daily','dailyc']);
			foreach ($Abils as $Abil) {
				$temp = array(
					'magic_id'  => (int)$Abil['magic_id'],
					'count'  	=> (int)$Abil['allcount'],
					'expire'	=> $Abil['findata'],
					'daily'		=> false,
				);
				if($Abil['daily']) {
					$temp['daily'] = array(
						'count' => $Abil['daily'],
						'have'	=> $Abil['dailyc'],
					);
				}

				$data['player']['playerabils'][] = $temp;
			}

			//account
			$Account = Effect::whereRaw('type in (4999, 5999, 6999) and owner = ?', [$User['id']])->first(['type', 'time']);
			if($Account) {
				$name = 'silver';
				if($Account['type'] == 5999) {
					$name = 'gold';
				} elseif($Account['type'] == 6999) {
					$name = 'platinum';
				}
				$data['player']['gamepaidstatus']['type'] = $name;
				$data['player']['gamepaidstatus']['date'] = (int)$Account['time'];
			}

			$response = array(
				'status'   => 1,
				'success' => true,
				//'crypt'     => $hash,
				'response'  => $data
			);

		} catch (\Exception $ex) {
			$response = array(
				'status'     => 0,
				'error' => true,
				//'crypt'     => $hash,
				'message'   => $ex->getMessage(),
			);
			FileHelper::writeException($ex, 'api');
		}

		$this->renderJSON($response);
	}

	public function questAction()
	{
		$response = array();
		$data = array();
		try {
			if($this->app->webUser->isGuest()) {
				throw new \Exception('User not found');
			}

			$user_id = $this->app->webUser->getId();
			$User = User::find($user_id)->toArray();
			if(!$User) {
				throw new \Exception('Invalid USER');
			}

			/*$Quest = $this->app->quest
				->setUser($User)
				->get();
			foreach($Quest->getDescriptionsInfo() as $_quest) {
				$data[] = array(
					'name' 			=> $_quest[1],
					'description' 	=> $_quest[2]
				);
			}*/

			$response = array(
				'status'   => 1,
				'success' => true,
				//'crypt'     => $hash,
				'response'  => $data
			);

		} catch (\Exception $ex) {
			$response = array(
				'status'     => 0,
				'error' => true,
				//'crypt'     => $hash,
				'message'   => 'We have some problem, try later',
			);
			FileHelper::writeException($ex, 'api');
		}

		$this->renderJSON($response);
	}


	public function inventoryAction()
	{
		$response = array();
		$hash = null;
		try {
			if($this->app->webUser->isGuest()) {
				throw new \Exception('User not found');
			}
			$user_id = $this->app->webUser->getId();

			$User = User::find($user_id)->toArray();
			if(!$User) {
				throw new \Exception('Invalid USER');
			}

			$data = array(
				'date' => time(),
				'items' => array(),
			);


			$_where = [
				'i.owner = :owner',
				'i.setsale = 0', //не в продаже
				'i.type not in (77, 200)',
				'i.otdel != 62',
				'(i.prototype not between 3000 and 3030)',
				'(i.prototype not between 103000 and 103030)',
				'(i.prototype not between 3003000 and 3003100)',
				'(i.prototype not between 3003200 and 3003400)',
				'(i.prototype not between 1009999 and 1020001)',
				'(i.prototype not between 15550 and 15569)'
			];
			$sql = sprintf('select i.id, i.name, i.letter, i.dressed, i.goden, i.dategoden, i.duration, i.maxdur, i.massa, i.includemagic, i.includemagicname, i.includemagicdex, i.includemagicmax, i.includemagicuses from inventory i where %s', implode(' and ', $_where));

			$pdo = CapitalDb::connection()->getPdo();
			$stmt = $pdo->prepare($sql);
			$stmt->execute([':owner' => $User['id']]);
			$_items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($_items as $_item) {
				$temp = array(
					'id'            => (int)$_item['id'],
					'name'          => $_item['name'],
					'description'   => $_item['letter'],
					'magic'         => array(),
					'is_dressed'    => $_item['dressed'],
					'expire'        => $_item['goden'] > 0 ? $_item['dategoden'] : 0,
					'duration'      => array(
						'current'   => $_item['duration'],
						'max'       => $_item['maxdur'],
					),
					'mass'			=> $_item['massa'],
				);
				if($_item['includemagic']) {
					$temp['magic'] = array(
						'name'      => $_item['includemagicname'],
						'magic'     => $_item['includemagic'],
						'have_use'  => $_item['includemagicdex'],
						'max_use'   => $_item['includemagicmax'],
						'recharge'  => $_item['includemagicuses'],
					);
				}

				$data['items'][] = $temp;

				unset($temp);
				unset($_item);
			}

			$response = array(
				'status'   => 1,
				'success' => true,
				//'crypt'     => $hash,
				'response'  => $data
			);

		} catch (\Exception $ex) {
			$response = array(
				'status'     => 0,
				'error' => true,
				//'crypt'     => $hash,
				'message'   => 'We have some problem, try later',
				'debug' => $ex->getTraceAsString(),
				'debug2' => $ex->getMessage(),
			);

			FileHelper::writeException($ex, 'api');
		}

		$this->renderJSON($response);
	}


	public function ruineAction()
	{
		$response = array();
		$hash = null;
		try {
			if($this->app->webUser->isGuest()) {
				throw new \Exception('User not found');
			}
			$user_id = $this->app->webUser->getId();


			$User = User::find($user_id)->toArray();
			if(!$User) {
				throw new \Exception('Invalid USER');
			}

			$data = array(
				'date'      => time(),
				'id'        => $User['ruines'] ? $User['ruines'] : 0,
				'team'      => $User['id_grup'] ? 'red' : 'blue',
				'players'   => array(),
				'traps'     => array(),
			);
			$player_ids = array();
			if($User['ruines']) {
				$Map = RuinesMap::find($User['ruines'])->toArray();

				$UserList = User::whereRaw('ruines = ? and id_grup = ?', [$User['ruines'], $User['id_grup']])->get()->toArray();
				foreach ($UserList as $_user) {
					$frozen = -1;
					/** @var Effect $EffectPuti */
					$EffectPuti = Effect::whereRaw('name = "Путы" and type = 10 and owner = ?', [$_user['id']])->first();
					if($EffectPuti) {
						$EffectPuti = $EffectPuti->toArray();
						$frozen = ($EffectPuti['time'] - time()) / 60;
					}

					$Inventory = Inventory::whereRaw('owner = ? and bs_owner = 2', [$_user['id']])->get()->toArray();
					$items = array();
					foreach ($Inventory  as $_inventory) {
						$items[] = array(
							'title' => $_inventory['name'],
							'img'   => $_inventory['img'],
							'type'  => str_replace('.gif', '', $_inventory['img']),
						);
					}

					$room = $_user['room'] - $Map['rooms'];

					$player = array(
						'id'                => (int)$_user['id'],
						'login'             => $_user['login'],
						'level'             => (int)$_user['level'],
						'align'             => $_user['align'],
						'clan'              => $_user['klan'],
						'hp'                => (int)$_user['hp'],
						'hpfull'            => (int)$_user['maxhp'],
						'ruineslocation'    => $this->app->ruine[$room][0],
						'battle'            => (int)$_user['battle'],
						'frozen'            => $frozen,
						'items'             => $items,
					);

					$data['players'][] = $player;
					$player_ids[] = (int)$_user['id'];
				}

				/** @var RuinesItems[] $Items */
				$Items = RuinesItems::whereIn('extra', $player_ids)
					->whereRaw('type = 1 and name = "Ловушка"')
					->get()->toArray();
				foreach ($Items as $_item) {
					$room = $_item['room'] - $Map['rooms'];
					$data['traps'][] = $this->app->ruine[$room][0];
				}
			}

			$response = array(
				'status'   => 1,
				'success' => true,
				//'crypt'     => $hash,
				'response'  => $data
			);

		} catch (\Exception $ex) {
			$response = array(
				'status'     => 0,
				'error' => true,
				//'crypt'     => $hash,
				'message'   => 'We have some problem, try later',
			);
			FileHelper::writeException($ex, 'api');
		}

		$this->renderJSON($response);
	}

    public function gamehelpCacheAction() {
            $this->renderJSON(array(
                'ok' => 1,
            ));
    }

    public function questCacheAction()
    {
        $quest_id = $this->app->request->get('quest_id');
        $Quest = QuestList::whereRaw('id = ?', [$quest_id])->count();
        if(!$Quest) {
            $this->renderJSON(array(
                'status' => 0,
                'message' => 'Quest not found'
            ));
        }

        if(!$this->app->cache->isExisting('quest_list_'.$quest_id)) {
            $this->renderJSON(array(
                'status' => 1,
                'message' => 'Cache not found for quest '.$quest_id,
            ));
        }

        $this->app->cache->delete('quest_list_'.$quest_id);
        $this->renderJSON(array(
            'status' => 1,
            'message' => 'Cache remove for quest '.$quest_id,
        ));
    }

	public function settingsCacheAction()
	{
		if($this->app->request->get('key') !== 'XTVoEUoiDpAeGNQz6rFHGM5vbH') {
			FileHelper::write('Incorrect api key for settingsCacheAction', 'api_log');
			exit;
		}

		if(!$this->app->cache->isExisting('dbConfig')) {
			$this->renderJSON(array(
				'status' => 1,
				'message' => 'Cache not found for dbConfig',
			));
		}

		$this->app->cache->delete('dbConfig');
		FileHelper::write('Clear cache for settingsCacheAction', 'api_log');

		$this->renderJSON(array(
			'status' => 1,
			'message' => 'Cache remove for settings ',
		));
	}

	public function configKoCacheAction()
	{
		if($this->app->request->get('key') !== '289n45vgikawbn5dfgknj') {
			FileHelper::write('Incorrect api key for settingsCacheAction', 'api_log');
			exit;
		}

		/*if(!$this->app->cache->isExisting('configKo2')) {
			$this->renderJSON(array(
				'status' => 1,
				'message' => 'Cache not found for configKo',
			));
		}*/

		$builder = ConfigKoSettings::from('config_ko_settings as cks')
			->join('config_ko_main as ckm', 'ckm.id', '=', 'cks.main_id')
			->where('ckm.is_enabled', '=', 1)
			->select(['cks.*']);
		$config = $builder->get()->toArray();
		$this->app->cache->set('configKo2', $config);

		//$this->app->cache->delete('configKo');
		FileHelper::write('Clear cache for configKoCacheAction', 'api_log');
		$this->renderJSON(array(
			'status' => 1,
			'message' => 'Cache remove for config ko ',
		));
	}
}
