<?php

namespace components\Controller\auth;

use Carbon\Carbon;
use components\Controller\_base\BaseController;
use components\Eloquent\Bank;
use components\Eloquent\DeloMulti;
use components\Eloquent\IpLog;
use components\Eloquent\Ivents;
use components\Eloquent\PartnersUsers;
use components\Eloquent\Telegraph;
use components\Eloquent\User;
use components\Eloquent\User2fa;
use components\Eloquent\UsersCounter;
use components\Eloquent\UsersSocialNetwork;
use components\Enum\AllowedLoginIp;
use components\Exceptions\LoginException;
use components\Helper\Geo;
use components\Helper\Oldbk;

/**
 * Class LoginController
 * @package components\Controller\auth
 * @property User $user
 */
class LoginController extends BaseController
{

    protected $layout = 'login';

    protected $ip;

    protected $ips = [];

    protected $user = null;

    protected $logException = true;

    /**
     * Login action
     */
    public function loginAction()
    {

        try {

            $request = $this->app->request;
            $session = $this->app->session;

            /*if ($request->isGet()) {
                $this->app->redirect($this->app->config('url.oldbk') . ($_SERVER['QUERY_STRING'] ? '/?' . $_SERVER['QUERY_STRING'] : ''));
            }*/

            $this->loginUser();

            $this->setIps();

            if (($userIp = AllowedLoginIp::getIpById($this->user['id'])) !== false) {
                if (in_array($userIp, $this->ips) === false) {
                    $this->user = null;
                }
            }

            //нет такого персонажа
            if (!$this->user) {
                throw new LoginException('login/usernotfound');
            }

            //перс в блоке
            if ($this->user->isBlocked()) {
                throw new LoginException('login/userblocked');
            }

            //запрет по гео
            if ($this->geoBlock()) {
                throw new LoginException('login/userblockedgeo');
            }

            $this->app->setCookie('battle', $this->user['id'], null, '/', '.' . \Config::get('cookies.domain'));

            //очищаем данные сессии
            $this->clearSessionData();

            //заходы другими персами
            $this->detectPreviousUser();

            //медалька за верность
            $this->giveMedal("005");

            //обновляем данные в блоге
            $this->authBlog();

            /**
             * @var User2fa $user2fa
             */
            $user2fa = $this->user->user2fa;

            //regenerate session
            $new_sid = $session::id(true);

            //Формируем данные сессии
            $session->set([
                'ip' => $request->getIp(),
                'sid' => $new_sid,
                'align' => ($this->user['align'] >= 1.9 && $this->user['align'] < 2 ? $this->user['align'] : null),
                'view' => $this->user['level'],
                ($this->user->hasSecondPassword() || ($user2fa && $user2fa->isEnabled()) ? "uid2" : "uid") => $this->user['id'],
                'adm_view' => (($this->user->isAdmin() && $this->user->isAdminion()) || $this->user['align'] == 7)
            ]);

            if (
                ($this->user['level'] >= 7 && (date("H") >= 9 && date("H") <= 19)) ||
                ($this->user['id'] == 14897 || $this->user['id'] == 703213)
            ) {

                $get_ingame = $this->user->usersInGame;

                $session->set([
                    'users_ingame' => ($get_ingame ? $get_ingame->toArray() : ["s2min" => 0, "s30min" => 0, "s60min" => 0]),
                    'users_time' => Carbon::now()->timestamp,
                ]);

            }

            /**
             * Google auth
             *
             * Гугл авторизация
             */
            if ($user2fa && $user2fa->isEnabled()) {
                $this->app->redirect($this->app->config('url.capital') . '/enter3.php');
            }

            /**
             * Second pass
             *
             * Авторизация вторым паролем
             */
            if ($this->user->hasSecondPassword()) {
                $this->app->redirect($this->app->config('url.capital') . '/enter2.php');
            }


            if ($new_sid != '') {

                //обновляем id сессии юзера
                $this->updateUserSid($new_sid);

                if ($session->get('adm_view') === false) {

                    //логирование в xml если чар есть в партнерке
                    if ($this->user->partnersUsers) $this->updateXmlData($this->user->partnersUsers);

                    //отправка в чат друг/враг
                    $this->friendsAndEnemies();

                }

                //счетчик заходов
                $this->usersCounter();

                //оповещалка заходов с дрругих айпи
                $this->ipSetups();

                //получаем телеграфы
                $this->telegraphsHandler();

                //текущие ивенты
                $this->iventsHandler();

                //замок на рюкзаке
                $this->boxIsOpen();

                //обновляем даты захода перса
                $this->updateUserDates();

                //оповещаем о шмотках со сроком годности
                $this->findItemsTimeOut();

                //бонусы за онлайн мальцам
                $this->doPresentItems();

                //редиректим в игрульку
                $this->app->redirect($this->app->config('url.capital') . '/battle.php');

            }

        } catch (LoginException $exception) {
            $this->app->session::destroy();
            $this->render($exception->getMessage());
        }

    }

    /**
     * @param $ex
     */
    private function logException(\Exception $ex)
    {
        if ($this->logException) {
            $this->app->getLog()->error($ex);
        }
    }

    /**
     * @return null|void
     */
    private function loginUser()
    {
//        $sn_session = $this->app->session->get('sn_user', []);
        $sn_session = $_SESSION['sn_user'] ?? [];

        $user = null;

        if ($sn_session && isset($sn_session['login'])) {

            $_data_ = User::where('login', $sn_session['login'])->first();

            if ($_data_ && ($_data_['pass'] == '' || $_data_['oldpass'] == $_data_['pass'])) {
                $this->app->session->set('chpass', $_data_['id']);

                return $this->app->redirect('http://capitalcity.oldbk.com/oldpass.php');
            }

            if ($_data_) {

                $sn_data = UsersSocialNetwork::where('user_id', $_data_['id'])
                    ->where('sn_id', $_data_['sn_id'])
                    ->where('sn_type', $_data_['sn_type'])
                    ->where('is_deleted', 0)
                    ->first();

                if ($sn_data) {
                    $user = $_data_;
                }

            }

            $this->app->session->delete('sn_user');

        } else {

            try {

                if (file_exists($file = "/www/oldbk.com/libs/asp.php")) {

                    require_once $file;

                    $tmp = [];

                    foreach ($__psarr as $k => $v) {
                        $tmp[strtolowermy($k)] = $v;
                    }

                    $mylog = strtolowermy(trim($this->app->request->post('login')));

                    if (isset($tmp[$mylog])) {
                        if ($tmp[$mylog]['p'] === $this->app->request->post('psw')) {
                            $user = User::where('id', $tmp[$mylog]['i'])->where('login', $mylog)->first();
                        }
                    } else {
                        throw new \Exception();
                    }
                } else {
                    throw new \Exception();
                }

            } catch (\Exception $e) {
                $password = $this->app->request->post('psw');

                $tmp = str_ireplace("'", "&#39;", $password);
                $tmp = str_ireplace("<", "&lt;", $tmp);
                $tmp = str_ireplace(">", "&gt;", $tmp);
                $tmp = str_ireplace("|", "&#0124;", $tmp);
                $tmp = str_ireplace("`", "&#96;", $tmp);


                $ff = in_smdp(htmlspecialchars($password));
                $ff_2 = in_smdp_new($tmp);

                $user = User::where('login', $this->app->request->post('login'))
                    ->where('pass', '!=', '')
                    ->where(function ($q) use ($ff, $ff_2) {

                        $q->where('pass', $ff);
                        $q->orWhere(function ($q2) use ($ff_2) {

                            $q2->where('pass', $ff_2);
                            $q2->where(function ($q3) {

                                $q3->whereRaw('pass = oldpass');
                            });
                        });
                    })
                    ->first();

                if ($user) {
                    $this->app->session->set('chpass', $user['id']);

                    return $this->app->redirect('http://capitalcity.oldbk.com/oldpass.php');
                }

                /** @var User $user */
                $user = User::where('login', $this->app->request->post('login'))
                    ->where('pass', '!=', '')
                    ->first();

                if($user && $user->validatePassword($password) === false) {
                    $user = null;
                }
            }
        }

        return $this->user = $user;

    }

    /**
     * Очистка данных сессии
     */
    private function clearSessionData()
    {
        $this->app->session->delete([
            'users_ingame',
            'users_time',
            'bankid',
            'boxisopen',
            'uid',
            'uid2',
            'sid',
            'view',
            'ip',
            'oldalg',
            'beginer_quest',
            'quest',
            'questdata',
            'questid',
            'KO_login',
            'adm_view',
        ]);
    }

    /**
     * Устанавливаем айпи клиента
     */
    private function setIps()
    {
        $this->ips = $this->app->request->getIps();
        $this->ip = implode('|', $this->ips);
    }

    /**
     * Логирование айпишников
     */
    private function ipLog()
    {
        IpLog::create([
            'owner' => $this->user['id'],
            'ip' => $this->ip,
            'date' => time(),
        ]);
    }

    /**
     * @param $medal
     *
     * выдаем медальку
     * @return bool
     */
    private function giveMedal($medal)
    {
        if (Carbon::now()->diffInYears(Carbon::parse($this->user['borntime'])) >= 3) {

            if (Oldbk::checkMedal($medal, $this->user['medals']) === false) {
                return $this->user->update([
                    'medals' => \DB::raw('CONCAT("' . $medal . ';", medals)'),
                ]);
            }

        }
    }

    /**
     * Обновляем данные в блоге
     */
    private function authBlog()
    {
        try {

            return (new \GuzzleHttp\Client())->get('https://blog.oldbk.com/api/refresh.html', [
                'query' => [
                    'game_id' => $this->user['id'],
                ],
                //'connect_timeout' => 1,
                'headers' => [
                    'Connection' => 'close',
                    'Host' => 'blog.oldbk.com',
                ],
            ]);

        } catch (\Exception $exception) {
            $this->logException($exception);
        }
    }

    /**
     * Заходы предыдущими персами
     * Записываем в ЛД
     */
    private function detectPreviousUser()
    {
        $battle = $this->app->getCookie('battle');

        if ($battle !== null && $this->user['id'] != $battle) {
            if (!$this->user->isAdmin() && !$this->user->isAdminion() && !$this->user->isHighPaladin()) {
                DeloMulti::create([
                    'idperslater' => $battle,
                    'idpersnow' => $this->user['id'],
                ]);
            }
        }
    }

    /**
     * @return bool
     *
     * Блокировка по гео
     */
    private function geoBlock()
    {
        $block = false;

        if ($effect = $this->user->getEffects([9898, 9899])->first()) {

            Geo::init();

            $codesCollection = collect();

            foreach ($this->ips as $k => $ip) {
                if (strpos($ip, ':') !== false) {
                    $ccountry = geoip_country_code_by_addr_v6(Geo::$gi6, $ip);
                } else {
                    $ccountry = geoip_country_code_by_addr(Geo::$gi, $ip);
                }

                if ($ccountry) {
                    $codesCollection->put($ip, $ccountry);
                }
            }

            if ($codesCollection->isNotEmpty()) {
                switch ($effect['type']) {
                    case 9898:
                        {
                            if (!$codesCollection->contains($effect['add_info'])) {
                                $block = true;
                            }

                            break;
                        }
                    case 9899:
                        {
                            if ($codesCollection->contains($effect['add_info'])) {
                                $block = true;
                            }

                            break;
                        }
                }
            }

        }

        return $block;
    }

    /**
     * @param $new_sid
     */
    private function updateUserSid($new_sid)
    {
        $this->user->update([
            'sid' => $new_sid,
        ]);
    }

    /**
     * @param PartnersUsers $partner
     * @return bool|mixed
     */
    private function updateXmlData(PartnersUsers $partner)
    {
        return Oldbk::updateXmlData($this->user['id'], $partner['partner'], 11, 1);
    }

    /**
     *
     */
    private function friendsAndEnemies()
    {
        if (!$this->user) {
            return false;
        }

        if (!$this->user->isHidden()) {
            $text = "Вас приветствует <a href=javascript:top.AddTo(\"" . $this->user['login'] . "\")><span oncontextmenu=\"return OpenMenu(event,8)\">" . $this->user['login'] . "</span></a>!   ";
            Oldbk::sendSystems($text, $this->user);
        }

        $f_query = \DB::table('friends as f')
            ->join('users as u', 'u.id', '=', 'f.owner')
            ->leftJoin('users as u1', 'u1.id', '=', 'f.friend')
            ->where('f.friend', $this->user['id'])
            ->where(function ($q) {

                $q->where('type', 0);
                $q->orWhere('type', 2);
            })
            ->where('u.hidden', 0)
            ->where('u.ldate', '>', Carbon::now()->subMinutes(3)->timestamp)
            ->get([
                'f.*',
                'u.login as owlogin',
                'u.id as ownid',
                'u.show_advises',
                'u1.login as frlogin',
            ]);


        if ($f_query->isNotEmpty()) {

            $target = [
                'friends' => [],
                'enemies' => [],
            ];

            foreach ($f_query as $row) {

                $show_advises = explode(',', $row->show_advises);

                if (isset($show_advises[1]) && $show_advises[1] == '1' && $row->type == 0) {
                    $target['friends'][] = $row->ownid;
                } else {
                    if (isset($show_advises[5]) && $show_advises[5] == '1' && $row->type == 2) {
                        $target['enemies'][] = $row->ownid;
                    }
                }

            }

            if (count($target['friends']) > 0) {
                $txt = "Вас приветствует <a href=javascript:top.AddTo(\"" . $this->user['login'] . "\")><span oncontextmenu=\"return OpenMenu(event,8)\">" . $this->user['login'] . "</span></a>!";
                if (!$this->user->isHidden()) {
                    Oldbk::sendGroup($txt, $target['friends']);
                }
            }

            if (count($target['enemies']) > 0) {
                $txt = "Вас приветствует ваш враг <a href=javascript:top.AddTo(\"" . $this->user['login'] . "\")><span oncontextmenu=\"return OpenMenu(event,8)\">" . $this->user['login'] . "</span></a>!";
                if (!$this->user->isHidden()) {
                    Oldbk::sendGroup($txt, $target['enemies']);
                }
            }

        }
    }

    /**
     * инкремент счетчика входов юзера
     */
    private function usersCounter()
    {
        return UsersCounter::updateOrCreate(
            [
                'owner' => $this->user['id'],
                'logdate' => Carbon::now()->toDateString(),
            ],
            [
                'count' => \DB::raw('count + 1'),
            ]
        );
    }

    /**
     * ip handler/log
     */
    private function ipSetups()
    {
        if (!$this->user) {
            return false;
        }

        $last_ip = IpLog::select([
            \DB::raw('MAX(id) as id'),
            'ip',
            'owner',
        ])
            ->where('owner', '=', $this->user['id'])
            ->groupBy('id', 'ip')
            ->orderBy('id', 'desc')
            ->first();

        $get_ip_setups = $this->user['gruppovuha'];

        if (isset($get_ip_setups[6]) && $get_ip_setups[6] == 1) {
            if ($last_ip && ($last_ip['ip'] != $this->ip) && ($last_ip['id'] > 0)) {
                $text = '<font color=red>Внимание!</font> В предыдущий раз вашим персонажем заходили с другого IP(' . $last_ip['ip'] . ').';
                Oldbk::sendPrivate($text, '{[]}' . $this->user['login'] . '{[]}', $this->user);
            }
        }

        if (isset($get_ip_setups[9]) && $get_ip_setups[9] > 0) {
            $getbank = Bank::where('id', $get_ip_setups[9])
                ->where('owner', $this->user['id'])
                ->first();

            if ($getbank) {
                $this->app->session->set('bankid', $getbank['id']);
            }
        }

        //логируем айпи захода
        $this->ipLog();
    }

    /**
     * Показываем сообщения из телеграфа
     */
    private function telegraphsHandler()
    {
        $telegraphs = Telegraph::where('owner', $this->user['id'])->get();
        if ($telegraphs->count()) {
            foreach ($telegraphs as $telegraph) {
                Oldbk::sendPrivate($telegraph['text'], '{[]}' . $this->user['login'] . '{[]}', $this->user);
                $telegraph->delete();
            }
        }
    }

    /**
     * Текущие ивенты
     */
    private function iventsHandler()
    {
        if ($ivents = Ivents::where('stat', 1)->first()) {
            $text = '<font color=red>Внимание!</font> ' . $ivents['info'];
            Oldbk::sendPrivate($text, '{[]}' . $this->user['login'] . '{[]}', $this->user);
        }
    }

    /**
     *
     */
    private function boxIsOpen()
    {
        if (!$this->app->session->get('boxisopen')) {
            if (!$this->user->hasEffect(88)) {
                $this->app->session->set('boxisopen', 'open');
            }
        }
    }

    /**
     * Обновляем даты
     */
    private function updateUserDates()
    {
        if (($this->user['id'] != 3) && ($this->user['id'] != 4)) {
            if ($this->user['align'] != 2.4) {
                $this->user->update([
                    'odate' => time(),
                    'ldate' => time(),
                ]);
            }
        }
    }

    /**
     * @return bool
     *
     * Срок годности предметов
     */
    private function findItemsTimeOut()
    {
        if (!$this->user) {
            return false;
        }

        return Oldbk::itemsTimeOut($this->user);
    }

    /**
     * @return bool
     *
     * Плюхи нубам за возвращение в игру после 30 дней
     */
    private function doPresentItems()
    {
        if (!$this->user) {
            return false;
        }

        if ($this->user['level'] <= 7) {

            if ((Carbon::now()->diffInDays(Carbon::createFromTimestamp($this->user['ldate']))) >= 30) {//30 дней

                /**
                 * 0 - proto
                 * 1 - pres author
                 * 2 - info
                 * 3 - goden days
                 * 4 - count
                 * 5 - getform
                 * 6 - sys message
                 * 7 - not sell
                 */

                $presents = [
                    0 => [//Сытный завтрак 1 шт.
                        0 => 105103,
                        1 => 'Удача',
                        2 => 'Бонус по возвращению',
                        3 => 0,
                        4 => 1,
                        5 => 20,
                        6 => false,
                        7 => true,
                    ],
                    1 => [//Малый свиток «Пропуск в Лабиринт» 1 шт. (срок годности 3 дня)
                        0 => 4005,
                        1 => 'Удача',
                        2 => 'Бонус по возвращению',
                        3 => 3,
                        4 => 1,
                        5 => 20,
                        6 => false,
                        7 => true,
                    ],
                ];

                $presented = Oldbk::doPresent($presents, $this->user);

                if (!empty($presented)) {
                    Oldbk::telegraph($this->user, '<font color=red>С возвращением!</font> Вы получили в подарок ' . (implode(' и ', $presented)) . ', предметы находятся у вас в Инвентаре. Удачной игры!');

                    return true;
                }

            }

        }

        return false;
    }
}
