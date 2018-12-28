<?php
namespace components\Controller\auth;

use components\Component\Slim\Slim;
use components\Controller\_base\BaseController;
use components\Component\Slim\Middleware\ClientScript\ClientScript;
use components\Eloquent\BeginersQuestsStep;
use components\Eloquent\Inventory;
use components\Eloquent\Invites;
use components\Eloquent\PartnersUsers;
use components\Eloquent\RegistrationIP;
use components\Eloquent\RidRefs;
use components\Eloquent\SocialNetwork;
use components\Eloquent\Telegraph;
use components\Eloquent\User;
use components\Eloquent\RidUsers;
use components\Eloquent\UserAdvert;
use components\Eloquent\UsersPasCh;
use components\Eloquent\UsersReferals;
use components\Eloquent\UsersSocialNetwork;
use components\Eloquent\XmlData;
use components\Enum\City;
use components\Enum\Partners;
use components\Enum\Season;
use components\Exceptions\RegistrationException;
use Carbon\Carbon;
use components\Helper\Rvs;
use components\Validator\RegistrationValidator;


class RegisterController extends BaseController
{

    protected $layout = 'registration';

    protected $title = 'OldBK - Регистрация';
    protected $description = 'Новая бесплатная многопользовательская MMORPG онлайн игра «Старый Бойцовский Клуб - ОлдБК». Стань участником Бойцовского Клуба Комбатс!';

    public function indexAction()
    {

        try {

            $this->registerCssAndScripts();

            $request = $this->app->request();

            $pid = $request->params('pid', null);
            $rid = $request->params('rid', null);
            $sid = $request->params('sid', null);
            $subid = $request->params('subid', null);
            $vk = $request->params('from', null) == 'vk' ? $request->params('from') : null;
            $myvk = $request->params('myvk', 0);

            if ($vk) {
                if (is_null($this->app->session->get('new_vk_id', null)) && $myvk > 0) {
                    $this->app->session->set('new_vk_id', (int)$myvk);
                }
            }


            if ($part = $this->app->getCookie('part')) {
                $pid = $part;
            }

            if ($rid) {
                $this->app->session->set('rid', trim($rid));
            }

            if ($subid) {
                $this->app->session->set('ts_subid', $subid);
            }


            $sn_data = null;
            if ($sid) {
                $this->app->session->set('sid', $sid);
                $sn_data = $this->checkSocialNetwork($sid);
            }

            //get season by date
            $season = Season::getSeason(Carbon::now()->quarter);

            $this->render('registration/index', [
                'season' => $season,
                'sn_data' => $sn_data,
            ]);
        } catch (RegistrationException $exception) {

        }

    }

    public function saveAction()
    {

        $request = $this->app->request();

        if ($request->isGet()) {
            return $this->app->redirectTo('registration', $request->get());
        }


        $login = trim($request->post('login'));
        $sex = $request->post('sex');
        $email = $request->post('email');
        $ip = $request->getIp();
        $sn_data = null;

        //Валидация входящих данных регистрации
        try {

            if ($this->app->getCookie('reg_time', null)) {
                throw new RegistrationException('Нельзя регистрироваться чаще чем раз в час. !');
            }

            $login = \Xss::clean($login);

            $rvs = Rvs::detect(['login' => $login],[
                'login' => [
                    'rvs_links'
                ]
            ]);

            if ($rvs !== false) {
                throw new RegistrationException('Использовать такой логин запрещено');
            }


            //Собственно сама валидация
            RegistrationValidator::validate(['login' => $login] + $request->params());

            //Проверка капчи
            if (!\components\Helper\Captcha::validate()) {
                throw new RegistrationException('Неверный защитный код!');
            }

            //Какая-то проверка ip из базы тора
            if ($this->test_tor_ip($ip)) throw new RegistrationException('Ошибка IP адреса!');

            //Проверяем время реги
            $this->checkRegTime($ip);

            //Проверяем соц сеть привязанную к игроку
            if ($sid =  $request->params('sid', $this->app->session->get('sid', null))) {
                $sn_data = $this->checkSocialNetwork($sid);

                if($sn_data){
                    throw new RegistrationException("Эта социальная сеть уже привязана к другому персонажу");
                }
            }


            //Город куда регистрировать
            $regCity = City::getCityName(City::$defaultRegistrationCity);

            //Соц сеть?
            $is_sn = $sn_data ? 1 : 0;

            //Берем мыло и пол из соц сети
            if ($is_sn) {
                if($sn_data['email']) {
                    $email = $sn_data['email'];
                }

                $sex = $sn_data['gender'];
            }

            $password = $request->post("psw");

            $salt = User::generateSalt();
            $password_hash = User::generatePassword($password, $salt);

            $saved = User::create([
                'vk_user_id' => $this->app->session->get('new_vk_id', 0),
                'borncity' => $regCity,
                'citizen' => $regCity,
                'login' => $login,
                'pass' => $password_hash,
                'salt' => $salt,
                'email' => $email,
                'realname' => '',
                'sex' => $sex,
                'color' => 'Black',
                'ip' => $ip,
                'room' => 2,
                'is_sn' => $is_sn,
                //'smagic' => 0,
                'gruppovuha' => array_fill(0, 9, 1),
            ]);

            if ($saved) {

                $this->app->setCookie('reg_time', 'enter', Carbon::now()->addHour()->timestamp);
                $this->app->session->set('reg_id', $saved->id);

                //Записываем IP при регистрации
                $this->registerIp($ip);

                //Если из соц сети, чето пишем
                if($is_sn) {
                    $this->registerSocialNetwork($saved, $sn_data);
                }

                //reg id
                if ($rid = $this->app->session->get('rid')) {
                    $this->registerRid($rid, $saved);
                }

                //referals
                if (($fr = $request->get('fr')) > 0 && ($frr = $request->get('frr')) > 0) {
                    $this->registerReferrals($saved, $login, $fr, $frr);
                }

                //начальный квест
                $this->registerBeginersQuestsStep($saved);

                //установка времени обновления пароля
                $this->registerUsersPasCh($saved, $login);

                //наполняем инвентарь
                $this->fillInventory($saved);

                //инвайты какие-то ???
                $this->registerInvites($saved, $login);

                //subids
                if ($ts_subid = $this->app->session->get('ts_subid')) {
                    $this->registerSubId($saved, $ts_subid);
                }

                //partners
                if ($pid = $request->params('pid', null)) {

                    $rt = time();

                    $this->registerPartnersUsers(
                        $saved,
                        $ip,
                        $pid,
                        $request->params('b'),
                        $request->params('ref'),
                        $rt
                    );

                    $this->registerXmlData($saved,$rt, $pid);
                }

                $this->app->flash('login', $login);
                $this->app->flash('psw', $request->post("psw"));
                $this->app->flash('pid', $request->params("pid"));
                $this->app->session->set('end_reg', 1);

                return $this->app->redirect($this->app->urlFor('registration', array_merge($request->get(), ['action' => 'complete'])));

            }

            throw new RegistrationException("Что-то пошло не так ;(");

        } catch (RegistrationException $e) {

            //Сохраняем поля для input так как мы в Exception'e ))
            $this->app->flash('login', $login);
            $this->app->flash('email', $request->post("email"));
            $this->app->flash('sex', $request->post("sex"));


            //Записываем эроры для отображения
            $this->app->flash('errors', [
                $e->getMessage()
            ]);

            //Так как что-то пошло не так, редиректим на страницу реги с гет параметрами, если были
            $this->app->redirectTo('registration', $request->get());
        }

    }

    /**
     * @return string|void
     * @throws \Exception
     */
    public function completeAction()
    {
        $this->registerCssAndScripts();
        $flashData = $this->app->flashData();

        if (!$this->app->session->get('end_reg')) {
            return $this->app->redirectTo('registration', $this->app->request->get());
        }

        $this->app->session->delete('end_reg');

        return $this->render('registration/complete', [
            'pid' => $flashData['pid'] ? Partners::getContent($flashData['pid']) : '',
            'login' => $flashData['login'],
            'psw' => $flashData['psw'],
        ]);
    }

    /**
     * @param $ip
     */
    protected function registerIp($ip)
    {
        try {
            RegistrationIP::create([
                'ip' => $ip,
                'time' => Carbon::now()->timestamp
            ]);
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $saved
     * @param $sn_data
     */
    protected function registerSocialNetwork($saved, $sn_data)
    {
        try {
            $_params = array(
                'user_id' => $saved->id,
                'sn_type' => $sn_data['sn_type'],
                'sn_id' => $sn_data['sn_id'],
                'created_at' => Carbon::now()->timestamp,
            );

            UsersSocialNetwork::create($_params);
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $rid
     * @param $saved
     */
    protected function registerRid($rid, $saved)
    {
        try {
            if (User::find($rid)) {

                RidUsers::firstOrCreate([
                    'owner' => $rid
                ]);

                RidRefs::create([
                    'ref' => $saved->id,
                    'owner' => $rid,
                    'when' => time(),
                ]);
            }
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $frr
     * @param $login
     */
    protected function registerTelegraph($frr, $login)
    {
        try {
            Telegraph::create([
                'owner' => $frr,
                'date' => '',
                'text' => '<font color=red>Внимание!</font> По вашей реферальной ссылке зарегистрировался персонаж <b>'.$login.'</b> ',
            ]);
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $fr
     * @param $frr
     * @param $saved
     */
    protected function registerUsersReferals($fr, $frr, $saved)
    {
        try {
            UsersReferals::create([
                'user' => $saved->id,
                'ref' => $fr,
                'owner' => $frr,
            ]);
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $saved
     */
    protected function registerBeginersQuestsStep($saved)
    {
        try {
            BeginersQuestsStep::create([
                'owner' => $saved->id,
                'quest_id' => 1,
            ]);
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $saved
     * @param $login
     */
    protected function registerUsersPasCh($saved, $login)
    {
        try {
            UsersPasCh::updateOrCreate(
                ['owner' => $saved->id, 'login' => $login],
                ['last' => time()]
            );
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $saved
     * @param $login
     */
    protected function registerInvites($saved, $login)
    {
        try {
            $invites = [];
            for ($ik = 0; $ik <= 4; $ik++) {
                $invites[] = [
                    'owner' => $saved->id,
                    'unic' => rand(1000000000,time()),
                ];
            }

            Invites::insert($invites);

            if ($unic = $this->app->request->get('u')) {
                Invites::where('unic', '=', $unic)->update(['whoreg' => $login]);
            }
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $saved
     * @param $ts_subid
     */
    protected function registerSubId($saved, $ts_subid)
    {
        try {

            $_data_ = [
                'user_id' => $saved->id,
                'ts_id' => 1,
                'cpa' => $ts_subid,
                'status' => 'hold',
                'need_send_postback' => 1,
                'updated_at' => time(),
                'created_at' => time(),
            ];

            foreach ($_data_ as $key => $value) {
                $_data_[$key] = addslashes(trim($value));
            }

            UserAdvert::create($_data_);

        } catch (\Exception $ex) {

        }
    }

    /**
     * @param $saved
     * @param $ip
     * @param $pid
     * @param $banner
     * @param $ref
     * @param $reg_time
     */
    protected function registerPartnersUsers($saved, $ip, $pid, $banner, $ref, $reg_time)
    {
        try {

            PartnersUsers::create([
                'id' => $saved->id,
                'ip' => $ip,
                'partner' => $pid,
                'banner' => $banner,
                'from_site' => $ref,
                'reg_time' => $reg_time,
            ]);

        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $saved
     * @param $rt
     * @param $pid
     */
    protected function registerXmlData($saved, $rt, $pid)
    {
        try {

            XmlData::create([
                'user_id' => $saved->id,
                'added_at' => Carbon::createFromTimestamp($rt)->format('Y-m-d'),
                'param_id' => 8,
                'value' => $rt,
                'pid' => $pid,
                'stamp' => $rt,
            ]);

        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $saved
     * @param $login
     * @param $fr
     * @param $frr
     */
    protected function registerReferrals($saved, $login, $fr, $frr)
    {
        if (User::find($frr)) {
            $this->registerTelegraph($frr, $login);
            $this->registerUsersReferals($fr, $frr, $saved);
        }
    }

    /**
     * @param $saved
     */
    protected function fillInventory($saved)
    {

        $items = [
            [
                'owner'     => $saved->id,
                'ghp'       => 3,
                'name'      => 'Рубашка',
                'type'      => 28,
                'massa'     => 1,
                'cost'      => 1,
                'img'       => 'roba1.gif',
                'maxdur'    => 10,
                'present'   => 'Мироздатель',
                'prototype' => 500,
            ],
            [
                'owner'     => $saved->id,
                'name'      => 'Сытный завтрак',
                'type'      => 50,
                'massa'     => 1,
                'cost'      => 0,
                'img'       => 'zavtrak_3average.gif',
                'maxdur'    => 10,
                'present'   => 'Мироздатель',
                'magic'     => 8,
                'otdel'     => 6,
                'isrep'     => 0,
                'prototype' => 105103,
                'notsell'   => 1,
                'goden'     => 30,
                'dategoden' => Carbon::now()->addDay(30)->timestamp,

            ],
            [
                'owner'     => $saved->id,
                'name'      => 'Шаг назад (мф.)',
                'type'      => 12,
                'massa'     => 1,
                'cost'      => 0,
                'img'       => 'downgrade.gif',
                'maxdur'    => 5,
                'present'   => 'Судьба',
                'magic'     => 2,
                'otdel'     => 6,
                'isrep'     => 0,
                'prototype' => 3,
            ],
            [
                'owner'     => $saved->id,
                'name'      => 'Мешок Новичка',
                'type'      => 3,
                'massa'     => 1,
                'cost'      => 1,
                'img'       => 'mesh2.gif',
                'maxdur'    => 10,
                'present'   => 'Мироздатель',
                'gmeshok'   => 50,
                'goden'     => 14,
                'dategoden' => Carbon::now()->addDay(14)->timestamp,
                'prototype' => 632,
                'otdel'     => 6,
            ]
        ];

        array_map(function ($item){
            Inventory::create($item);
        }, $items);

    }

    /**
     *
     */
    private function registerCssAndScripts()
    {
        $this->app->clientScript
            ->registerCssFile('/assets/bootstrap/css/bootstrap.min.css')
            ->registerCssFile('/assets/adaptive/css/font-awesome.min.css')
            ->registerCssFile('/assets/adaptive/css/kp-new-style.css')
            ->registerCssFile('/assets/adaptive/css/img.min.css')
            ->registerCssFile('/assets/register/css/register.css');

        $this->app->clientScript
            ->registerJsFile('/js/advert.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('/assets/jquery/jquery.min.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/bootstrap/js/bootstrap.bundle.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/home/js/jquery.boxloader.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/home/js/validator.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/js/cufon-yui.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/js/Stylo_700.font.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/register/js/js.reg.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/js/gatracking/gat.js', ClientScript::JS_POSITION_BEGIN);
    }

    /**
     * @param $ip
     * @return bool
     */
    private function test_tor_ip($ip)
    {
        $tor_file = ROOT_DIR . '/tor/data';
        if (file_exists($tor_file)) {
            $lines = file($tor_file);
            foreach ($lines as $line_num => $line) {
                if ($ip == trim($line)) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    /**
     * @param $ip
     * @throws RegistrationException
     */
    private function checkRegTime($ip)
    {
        $check_data = RegistrationIP::where('ip', $ip)->orderBy('time', 'desc')->first();

        if ($check_data) {
            $check_reg_time = 3600 - (time() - $check_data['time']);
            $check_reg_time = round($check_reg_time/60, 0);
            if ($check_reg_time > 0) {
                throw new RegistrationException("С Вашего IP адреса можно будет зарегистрироваться не ранее чем через $check_reg_time. минут");
            }
        }
    }

    /**
     * @param $sid
     * @return mixed
     */
    private function checkSocialNetwork($sid)
    {
        return SocialNetwork::where('sid', $sid)->first();
    }

    /**
     * @return bool
     */
    public function checkloginAction()
    {

        $response = [
            'status' => 1,
            'exist' => false,
        ];


        $request = $this->app->request();

        if (!$request->isAjax()) {
            return false;
        }

        $login = trim($request->post('login'));

//        $login = iconv("UTF-8", "windows-1251", $login);

        try {

            \DB::select(\DB::raw('SET NAMES utf8'));
            $user = User::select(['id'])
                ->where('login', '=', $login)
                ->first();

            if ($user) {
                $response['exist'] = true;
            }

        } catch (\Exception $ex) {
            $response = [
                'status' => 0,
                'error' => $ex->getMessage(),
            ];
        }

        $this->renderJSON($response);

    }

    /**
     * @param $_view
     * @param null $_data_
     * @param bool $_return
     * @return string
     * @throws \Exception
     */
    public function render($_view, $_data_ = null, $_return = false)
    {
        $this->app->view()->appendLayoutData(array(
            'page_title' => $this->title,
            'page_description' => $this->description
        ));

        return parent::render($_view, $_data_, $_return);
    }

}
