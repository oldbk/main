<?php
namespace components\Controller;


use Carbon\Carbon;
use components\Controller\_base\BaseController;
use components\Component\Slim\Middleware\ClientScript\ClientScript;
use components\Eloquent\Bank;
use components\Eloquent\ClansWar;
use components\Eloquent\ConfirmPassword;
use components\Eloquent\Ivents;
use components\Eloquent\Lichka;
use components\Eloquent\News;
use components\Eloquent\Partners;
use components\Eloquent\User;
use components\Enum\Season;
use components\Exceptions\ReminderPasswordException;

/**
 * Class HomeController
 * @package components\Controller
 */
class HomeController extends BaseController
{

    protected $layout = 'home_mob';

    protected $title = 'ОлдБК: Бойцовский Клуб - ролевая бесплатная онлайн мморпг игра | Играть бесплатно в браузерную игру';
    protected $description = 'Новая бесплатная многопользовательская MMORPG онлайн игра «Старый Бойцовский Клуб - ОлдБК». Стань участником Бойцовского Клуба Комбатс!';

	protected $http_fix_enable = true;

    /**
     * @throws \Exception
     */
    public function indexAction()
    {

        $request = $this->app->request;

        $reg = $request->get('reg', null);// reg
        $fr = $request->get('fr', null);// ref id

        $pid = $request->get('pid', null);//partner id
        $b = $request->get('b', null);// banner

        $ref = $request->get('ref', null);//referer

        $rid = $request->get('rid', null);//registration id


        //Если есть параметр reg редиректим на регу
        if (!is_null($reg)) {
            return $this->app->redirectTo('registration', $request->get());
        }


        //Если есть айди партнера , инкрементим счетчик
        if (!is_null($pid) && !is_null($b)) {

            if ($partner = Partners::where('id', $pid)->first()) {
                $partner->increment('click_b' . $b);
            }

        }


        //Если есть параметр rid редиректим на регу
        if ($rid > 0) {
            $this->app->session->set('rid', $rid);
            return $this->app->redirectTo('registration', $request->get());
        }

        //referal id
        if ($fr > 0) {

            $this->title = intval($fr) . ' | ' . $this->title;

            if (
                !$this->app->session->get('referal', false) &&
                $this->app->session->get('referal_b', false) !== 1
            ) {

                $bank = Bank::where('id', $fr)->first(['id', 'owner']);

                if ($bank) {
                    if ($bank['id'] == $fr) {

                        $this->app->session->set('referal', $bank['id']);
                        $this->app->session->set('referal_own', $bank['owner']);

                        return $this->app->redirectTo('registration', [
                                'reg' => 1,
                                'fr' => $bank['id'],
                                'frr' => $bank['owner'],
                            ] + $request->get());

                    } else {
                        $this->app->session->set('referal_b', 1);
                    }
                }

            }
        }

        ///////////////////////////

        //тут данные для правой колнки
        $this->prepareLayoutData();


        //Если есть гет remem - Редерим страницу "Восстановление пароля"
        /*if ($request->get('remem', false)) {
            $this->rememberAction();
        }*/


        //Если есть гет about - Редерим страницу "Об игре"
        if ($request->get('about', false)) {

            $this->app->view()->appendLayoutData([
                'all_news' => false,
            ]);

            return $this->render('home/about');
        }

        //Если есть гет helpers - Редерим страницу "Помощь по игре"
        if ($request->get('helpers', false)) {

            $this->app->view()->appendLayoutData([
                'all_news' => false,
            ]);

            return $this->render('home/helpers');
        }


        //Новости на главную
        if ($this->app->cache->isExisting('home_news')) {
            $news = $this->app->cache->get('home_news');
        } else {
            $news = News::homeNews()->limit(6)->get();
            $this->app->cache->set('home_news', $news, 3600);
        }

        //Текущие клан вары
        if ($this->app->cache->isExisting('clans_wars')) {
            $wars = $this->app->cache->get('clans_wars');
        } else {
            $wars = ClansWar::currentWars()->get();
            $this->app->cache->set('clans_wars', $wars, 3600);
        }

        $this->app->view()->appendLayoutData([
            'all_news' => true,
        ]);


        $this->render('home/index_mob', [
            'news'      => $news,
            'wars'      => $wars,
            'comm_data' => $this->commData(),
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function reminderAction()
    {
        $request = $this->app->request;

        $this->title = 'Восстановление пароля';

        $this->prepareLayoutData();

        $this->app->view()->appendLayoutData([
            'all_news' => false,
        ]);


        try {

            //Если это пост запрос
            if ($request->isPost()) {

                $loginid = \Xss::clean($_POST['loginid']);
//                $loginid = \Xss::clean($request->post('loginid'));

                //Валидация входящих данных
                $validator = \Validator::make(
                    [
                        'loginid' => $loginid
                    ],
                    [
                        'loginid' => [
                            'required',
//                            'min:4',
//                            'max:20',
                        ],
                    ],
                    [
                        'loginid.required'  => 'reminder.loginid.required',
                        'loginid.min'       => 'reminder.loginid.min',
                        'loginid.max'       => 'reminder.loginid.max',
                    ]
                );

                if ($validator->fails()) {
                    throw new ReminderPasswordException($validator->errors()->first());
                }


                //Берем из базы по логину
                $user = User::whereLogin($loginid)->first([
                    'id',
                    'realname',
                    'login',
                    'email',
                    'klan',
                    'deal',
                    'pass',
                ]);


                //конец текущего дня (23:59:59) в unix timestamp
                $lasttime = Carbon::now()->endOfDay()->timestamp;

                $ipclient = getenv("HTTP_X_FORWARDED_FOR");
                $ip = $request->getIp();


                $confirm_exist = ConfirmPassword::where('ip', $ip)->orderBy('date', 'desc')->first();

                if (
                    $confirm_exist &&
                    Carbon::createFromTimestamp($confirm_exist['date'])->isToday() &&
                    $ip == $confirm_exist['ip']
                ) {
                    throw new ReminderPasswordException('Запрос пароля один раз в сутки');
                }


                //обработчик
                $result = $this->reminderPasswordHandler($user, $loginid);

                if (is_array($result)) {

                    $current_hour = Carbon::now()->hour;
                    $current_text = '';

                    switch(true) {

                        case ($current_hour >= 0 && $current_hour <= 5):
                        case ($current_hour >= 21 && $current_hour <= 24): {
                            $current_text = 'Доброй ночи';
                            break;
                        }

                        case ($current_hour > 5 && $current_hour <= 12): {
                            $current_text = 'Доброе утро';
                            break;
                        }

                        case ($current_hour > 12 && $current_hour <= 17): {
                            $current_text = 'Добрый день';
                            break;
                        }

                        case ($current_hour > 17 && $current_hour <= 21): {
                            $current_text = 'Добрый вечер';
                            break;
                        }

                    }

                    $msg = $this->sendMailWithPassword([
                        'login'         => $user->login,
                        'realname'      => $user->realname ?: $user->login,
                        'newpass'       => $result['newpass'],
                        'email'         => $user->email,
                        'ip'            => $ip,
                        'ipclient'      => $ipclient,
                        'current_text'  => $current_text,
                    ]);


                    if ($msg !== false) {
                        $this->app->flashNow('success', [
                            $msg,
                        ]);

                        //Запись в ЛД
                        Lichka::create([
                            'pers' => $user->id,
                            'text' => \Lang::get('lichka.password_was_sent', [
                                'ipclient' => $ipclient,
                                'ip' => $ip,
                            ]),
                            'date' => Carbon::now()->timestamp,
                        ]);

                        //Запись о запросе пароля
                        ConfirmPassword::create([
                            'login' => $user->login,
                            'date' => $lasttime,
                            'ip' => $ipclient." || ".$ip,
                            'active' => 1,
                        ]);

                        if ($result['old'] == 1) {

                            /*User::where('id', $user['id'])->update([
                                'pass' => (md5($result['newpass']))
                            ]);*/

                        }
                    }

                }

            }

        } catch (ReminderPasswordException $e) {
            //Записываем эроры для отображения
            $this->app->flashNow('errors', [
                $e->getMessage(),
            ]);
        }


        //рендерим страницу восстановления пароля
        return $this->render('home/reminder');
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function aboutAction()
    {

        $this->title = 'Об игре | ' . $this->title;

        $this->prepareLayoutData();


        $this->app->view()->appendLayoutData([
            'all_news' => false,
        ]);

        return $this->render('home/about');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function helpAction()
    {
        $this->title = 'Помощь по Игре | ' . $this->title;

        $this->prepareLayoutData();


        $this->app->view()->appendLayoutData([
            'all_news' => false,
        ]);

        return $this->render('home/help');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function screenAction()
    {
        $this->title = 'Скриншоты | ' . $this->title;

        $this->prepareLayoutData();


        $this->app->view()->appendLayoutData([
            'all_news' => false,
        ]);

        return $this->render('home/screen');
    }

    /**
     * @param $user
     * @param string $loginid
     * @return array
     * @throws ReminderPasswordException
     */
    private function reminderPasswordHandler($user, $loginid = '')
    {

        //Проверяем капчу
        if(!\components\Helper\Captcha::validate())  {
            throw new ReminderPasswordException("reminder.captcha_is_not_set");
        }


        //есть ли юзер?
        if ($user) {

            $user = $user->toArray();

            $confirm_exist = ConfirmPassword::where('login', $user['login'])->first();

            //Если запись в базе о запросе пароля
            if ($confirm_exist) {

                //Если сегодня пароль уже запрашивался выбрасываем экзепшн
                if (Carbon::createFromTimestamp($confirm_exist['date'])->isToday()) {
                        throw new ReminderPasswordException('reminder.password_is_was_sent');
                } else {
                    ConfirmPassword::where('login', $user['login'])->delete();//удаляем запись так как дата в прошлом
                }

            }

            //Есть ли мыло у юзера?
            if (!$user['email']) {
                throw new ReminderPasswordException('reminder.email_not_found', ['login' => $user['login']]);
            }


            if((($user['klan'] != 'Adminion') and ($user['klan'] != 'radminion')) and ($user['deal'] == 0)) {

                $pass = out_smdp($user['pass']);

                if (!$pass) {
                    $pass = out_smdp_new($user['pass']);

                    if (!$pass) {
                        throw new ReminderPasswordException('reminder.password_error');
                    } else {
                        return [
                            'newpass' => $pass,
                            'old' => 0,
                        ];
                    }
                }
                else {
                    return [
                        'newpass' => $pass,
                        'old' => 0,
                    ];
                }
            } else {
                throw new ReminderPasswordException('reminder.deny_user');
            }

        } else {
            throw new ReminderPasswordException('reminder.user_not_found', ['login' => $loginid]);
        }

    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    private function sendMailWithPassword(array $data = [])
    {
        require_once( ROOT_DIR . '/mailer/send-email2.php');

        $mail = $this->renderPartial('email/reminder', $data, true);

        if(mailnew($data['email'], \Lang::get('reminder.email.subject', ['login' => $data['login']]), $mail, true)){

            $hidden_mail = mask_email($data['email']);

            return \Lang::get('reminder.email.success', ['hidden_mail' => $hidden_mail]);
        }

        return false;
    }

    /**
     *
     */
    private function prepareLayoutData()
    {

        //Кол-во зареганых игроков
        $count_registered_users = 0;
        if ($this->app->cache->isExisting('users_count')) {
            $count_registered_users = $this->app->cache->get('users_count');
        } else {
            $last_reg_user = User::select(['id'])->orderBy('id', 'desc')->first();

            if ($last_reg_user) {
                $count_registered_users = $last_reg_user->id;
                $this->app->cache->set('users_count', $count_registered_users, 3600);
            }
        }


        //Рейтинг побед
        if (!($rate_wins = $this->app->cache->get('rate_wins'))) {
            $rate_wins = User::rateWins()->get()->toArray();
            $this->app->cache->set('rate_wins', $rate_wins, 3600);
        }

        //Рейтинг черепов.
        if (!($rate_skulls = $this->app->cache->get('rate_skulls'))) {
            $rate_skulls = User::rateSkulls(7)
                ->union(User::rateSkulls(8))
                ->union(User::rateSkulls(9))
                ->union(User::rateSkulls(10))
                ->union(User::rateSkulls(11))
                ->union(User::rateSkulls(12))
                ->union(User::rateSkulls(13))
                ->union(User::rateSkulls(14))
                ->get()->groupBy('level')->toArray();
            $this->app->cache->set('rate_skulls', $rate_skulls, 3600);
        }

        //Рейтинг воинки
        if (!($rate_voins = $this->app->cache->get('rate_voins'))) {
            $rate_voins = User::rateVoins()->get()->toArray();
            $this->app->cache->set('rate_voins', $rate_voins, 3600);
        }

        //Рейтинг клановых войн
        if (!($rate_clan_wars = $this->app->cache->get('rate_clan_wars'))) {
            $rate_clan_wars = \DB::select(\DB::raw('select  clan, sum(kolwin) as kw , cl.short, cl.align, cl.name  from (select agressor as clan, count(winner) as kolwin from  clans_war_new where winner=1 group by agressor  union all select defender as clan, count(winner) as kolwin from  clans_war_new where winner=2 group by defender) t LEFT JOIN clans cl on cl.id=clan group by clan ORDER by kw desc  limit 10'));
            $this->app->cache->set('rate_clan_wars', $rate_clan_wars, 3600);
        }


        //Великие битвы
        if (!($rate_grand_battles = $this->app->cache->get('rate_grand_battles'))) {
            $rate_grand_battles = User::grandBattles()->get()->toArray();
            $this->app->cache->set('rate_grand_battles', $rate_grand_battles, 3600);
        }


        //Текущая неделя
        $ivent = Ivents::where('stat', 1)->first();

        //Текущее время года
        $season = Season::getSeason(Carbon::now()->quarter);


        //Аппендим данные в Layout (правая колонка рейтинги итд)
        $this->app->view()->appendLayoutData([
            'season' => $season,
            'registered' => $count_registered_users,
            'rate' => [
                'wins' => $rate_wins,
                'skulls' => $rate_skulls,
                'voins' => $rate_voins,
                'clan_wars' => (array)$rate_clan_wars,
                'grand_battles' => $rate_grand_battles,
            ],
            'ivent' => $ivent ? $ivent->toArray() : false,
        ]);
    }

    /**
     * @return array
     *
     * Акции комм отдела
     */
    protected function commData()
    {
        //TODO hardcode
        require_once ROOT_DIR . "/config_ko.php";
        require_once ROOT_DIR . "/ny_events.php";

        if ((time() > $KO_start_time7) and (time() < $KO_fin_time7)) {
            //покупка екр. старт акции
            $komlinks[] = array(
                "start" => $KO_start_time7,
                "fin" => $KO_fin_time7,
                "img" => $KO_A_IMG7,
                "title" => $KO_A_TITLE7,
                "text" => $KO_A_TXT7,
                "url" => $KO_A_URL7);

        }

        if ((time() > $KO_start_time48) and (time() < $KO_fin_time48)) {
            $komlinks[] = array(
                "start" => $KO_start_time48,
                "fin" => $KO_fin_time48,
                "img" => $KO_A_IMG48,
                "title" => $KO_A_TITLE48,
                "text" => $KO_A_TXT48,
                "url" => $KO_A_URL48);

        }

        if ((time() > $KO_start_time47) and (time() < $KO_fin_time47)) {
            $komlinks[] = array(
                "start" => $KO_start_time47,
                "fin" => $KO_fin_time47,
                "img" => $KO_A_IMG47,
                "title" => $KO_A_TITLE47,
                "text" => $KO_A_TXT47,
                "url" => $KO_A_URL47);

        }

        if ((time() > $KO_start_time46) and (time() < $KO_fin_time46)) {
            $komlinks[] = array(
                "start" => $KO_start_time46,
                "fin" => $KO_fin_time46,
                "img" => $KO_A_IMG46,
                "title" => $KO_A_TITLE46,
                "text" => $KO_A_TXT46,
                "url" => $KO_A_URL46);

        }

        if (time() > $KO_start_time2 && time() < $KO_fin_time2) {
            //образы и картинки
            $komlinks[] = array(
                "start" => $KO_start_time2,
                "fin" => $KO_fin_time2,
                "img" => $KO_A_IMG2,
                "title" => $KO_A_TITLE2,
                "text" => $KO_A_TXT2,
                "url" => $KO_A_URL2);
        }

        if ((time() > $KO_start_time30) and (time() < $KO_fin_time30)) {

            $komlinks[] = array(
                "start" => $KO_start_time30,
                "fin" => $KO_fin_time30,
                "img" => $KO_A_IMG30,
                "title" => $KO_A_TITLE30,
                "text" => $KO_A_TXT30,
                "url" => $KO_A_URL30);

        }

        if ((time() > $KO_start_time38) and (time() < $KO_fin_time38)) {

            $komlinks[] = array(
                "start" => $KO_start_time38,
                "fin" => $KO_fin_time38,
                "img" => $KO_A_IMG38,
                "title" => $KO_A_TITLE38,
                "text" => $KO_A_TXT38,
                "url" => $KO_A_URL38);

        }

        if ((time() > $KO_start_time45) and (time() < $KO_fin_time45)) {
            $komlinks[] = array(
                "start" => $KO_start_time45,
                "fin" => $KO_fin_time45,
                "img" => $KO_A_IMG45,
                "title" => $KO_A_TITLE45,
                "text" => $KO_A_TXT45,
                "url" => $KO_A_URL45);

        }

        if (time() > $KO_BOX_start_time && time() < $KO_BOX_fin_time) {
            //покупка ларцов и яиц
            $komlinks[] = array(
                "start" => $KO_BOX_start_time,
                "fin" => $KO_BOX_fin_time,
                "img" => $KO_BOX_EGGS_IMG, // картинка из конфига
                "title" => $KO_BOX_EGGS_TITLE,
                "text" => $KO_BOX_EGGS_TXT,
                "url" => $KO_BOX_EGGS_URL);
        }

        if ((time() > $KO_start_time42) and (time() < $KO_fin_time42)) {
            $komlinks[] = array(
                "start" => $KO_start_time42,
                "fin" => $KO_fin_time42,
                "img" => $KO_A_IMG42,
                "title" => $KO_A_TITLE42,
                "text" => $KO_A_TXT42,
                "url" => $KO_A_URL42);

        }

        if ((time() > $KO_start_time41) and (time() < $KO_fin_time41)) {
            $komlinks[] = array(
                "start" => $KO_start_time41,
                "fin" => $KO_fin_time41,
                "img" => $KO_A_IMG41,
                "title" => $KO_A_TITLE41,
                "text" => $KO_A_TXT41,
                "url" => $KO_A_URL41);

        }

        if ((time() > $KO_start_time18) and (time() < $KO_fin_time18)) {
            $komlinks[] = array(
                "start" => $KO_start_time18,
                "fin" => $KO_fin_time18,
                "img" => $KO_A_IMG18,
                "title" => $KO_A_TITLE18,
                "text" => $KO_A_TXT18,
                "url" => $KO_A_URL18);

        }

        if ((time() > $KO_start_time23) and (time() < $KO_fin_time23)) {

            $komlinks[] = array(
                "start" => $KO_start_time23,
                "fin" => $KO_fin_time23,
                "img" => $KO_A_IMG23,
                "title" => $KO_A_TITLE23,
                "text" => $KO_A_TXT23,
                "url" => $KO_A_URL23);

        }

        if ((time() > $KO_start_time40) and (time() < $KO_fin_time40)) {

            $komlinks[] = array(
                "start" => $KO_start_time40,
                "fin" => $KO_fin_time40,
                "img" => $KO_A_IMG40,
                "title" => $KO_A_TITLE40,
                "text" => $KO_A_TXT40,
                "url" => $KO_A_URL40);

        }

        if ((time() > $KO_start_time39) and (time() < $KO_fin_time39)) {

            $komlinks[] = array(
                "start" => $KO_start_time39,
                "fin" => $KO_fin_time39,
                "img" => $KO_A_IMG39,
                "title" => $KO_A_TITLE39,
                "text" => $KO_A_TXT39,
                "url" => $KO_A_URL39);

        }

        if ((time() > $KO_start_time26) and (time() < $KO_fin_time26)) {

            $komlinks[] = array(
                "start" => $KO_start_time26,
                "fin" => $KO_fin_time26,
                "img" => $KO_A_IMG26,
                "title" => $KO_A_TITLE26,
                "text" => $KO_A_TXT26,
                "url" => $KO_A_URL26);

        }

        if ((time() > $KO_start_time37) and (time() < $KO_fin_time37)) {

            $komlinks[] = array(
                "start" => $KO_start_time37,
                "fin" => $KO_fin_time37,
                "img" => $KO_A_IMG37,
                "title" => $KO_A_TITLE37,
                "text" => $KO_A_TXT37,
                "url" => $KO_A_URL37);

        }

        if ((time() > $KO_start_time36) and (time() < $KO_fin_time36)) {

            $komlinks[] = array(
                "start" => $KO_start_time36,
                "fin" => $KO_fin_time,
                "img" => $KO_A_IMG36,
                "title" => $KO_A_TITLE36,
                "text" => $KO_A_TXT36,
                "url" => $KO_A_URL36);

        }

        if ((time() > $KO_start_time35) and (time() < $KO_fin_time35)) {

            $komlinks[] = array(
                "start" => $KO_start_time35,
                "fin" => $KO_fin_time35,
                "img" => $KO_A_IMG35,
                "title" => $KO_A_TITLE35,
                "text" => $KO_A_TXT35,
                "url" => $KO_A_URL35);

        }

        if ((time() > $KO_start_time33) and (time() < $KO_fin_time33)) {

            $komlinks[] = array(
                "start" => $KO_start_time33,
                "fin" => $KO_fin_time33,
                "img" => $KO_A_IMG33,
                "title" => $KO_A_TITLE33,
                "text" => $KO_A_TXT33,
                "url" => $KO_A_URL33);

        }

        if ((time() > $KO_start_time25) and (time() < $KO_fin_time25)) {

            $komlinks[] = array(
                "start" => $KO_start_time25,
                "fin" => $KO_fin_time25,
                "img" => $KO_A_IMG25,
                "title" => $KO_A_TITLE25,
                "text" => $KO_A_TXT25,
                "url" => $KO_A_URL25);

        }

        if ((time() > $KO_start_time) and (time() < $KO_fin_time)) {
            //покупка екр. старт акции
            $komlinks[] = array(
                "start" => $KO_start_time,
                "fin" => $KO_fin_time,
                "img" => $KO_A_IMG,
                "title" => $KO_A_TITLE,
                "text" => $KO_A_TXT,
                "url" => $KO_A_URL);

        }

        if ((time() > $KO_start_time31) and (time() < $KO_fin_time31)) {

            $komlinks[] = array(
                "start" => $KO_start_time31,
                "fin" => $KO_fin_time31,
                "img" => $KO_A_IMG31,
                "title" => "Акция «Евро-2016»",
                "text" => "Флаги, мячи и подарки для настоящих фанатов!",
                "url" => $KO_A_URL31);

        }

        if ((time() > $KO_start_time29) and (time() < $KO_fin_time29)) {

            $komlinks[] = array(
                "start" => $KO_start_time29,
                "fin" => $KO_fin_time29,
                "img" => $KO_A_IMG29,
                "title" => $KO_A_TITLE29,
                "text" => $KO_A_TXT29,
                "url" => $KO_A_URL29);

        }
        
        if ((time() > $KO_start_time52) and (time() < $KO_fin_time52)) {

            $komlinks[] = array(
                "start" => $KO_start_time52,
                "fin" => $KO_fin_time52,
                "img" => $KO_A_IMG52,
                "title" => $KO_A_TITLE52,
                "text" => $KO_A_TXT52,
                "url" => $KO_A_URL52);

        }

		if ((time() > $KO_start_time53) and (time() < $KO_fin_time53)) {

			$komlinks[] = array(
				"start" => $KO_start_time53,
				"fin" => $KO_fin_time53,
				"img" => $KO_A_IMG53,
				"title" => $KO_A_TITLE53,
				"text" => $KO_A_TXT53,
				"url" => $KO_A_URL53);

		}

		if ((time() > $KO_start_time28) and (time() < $KO_fin_time28)) {

            $komlinks[] = array(
                "start" => $KO_start_time28,
                "fin" => $KO_fin_time28,
                "img" => $KO_A_IMG28,
                "title" => $KO_A_TITLE28,
                "text" => $KO_A_TXT28,
                "url" => $KO_A_URL28);

        }

        if (time() > $KO_start_time5 && time() < $KO_fin_time5) {
            //весна
            $komlinks[] = array(
                "start" => $KO_start_time5,
                "fin" => $KO_fin_time5,
                "img" => $KO_A_IMG5,
                "title" => $KO_A_TITLE5,
                "text" => $KO_A_TXT5,
                "url" => $KO_A_URL5);
        }

        if (time() > $KO_start_time4 && time() < $KO_fin_time4) {
            //зима
            $komlinks[] = array(
                "start" => $KO_start_time4,
                "fin" => $KO_fin_time4,
                "img" => $KO_A_IMG4,
                "title" => $KO_A_TITLE4,
                "text" => $KO_A_TXT4,
                "url" => $KO_A_URL4);
        }

        if (time() > $ny_events['elkadropstart'] && time() < $ny_events['elkadropend']) {
            $komlinks[] = array(
                "start" => $ny_events['elkadropstart'],
                "fin" => $ny_events['elkadropend'],
                "img" => "http://i.oldbk.com/i/action/slider_sized_winter4.jpg",
                "title" => '"Боевые елочки"',
                "text" => 'Возьми боевую елочку вместо оружия и стань сильнее в разы! Только в новогодние месяцы!',
                "url" => "http://oldbk.com/encicl/?/cap_flowers_kr_elki.html");

        }

        /*
        {
        //Противостояние
        $komlinks[] =  array(
                    "img" => "http://i.oldbk.com/i/action/banner_422.jpg",
                    "title" => 'Противостояние',
                    "text" => 'Отстаивайте честь вашей склонности, впишите свои имена в рейтинг собирателей черепов!',
                    "url" => 'http://oldbk.com/encicl/?/protivost.html',
            );

        }
        */

        if ((time() > $KO_start_time22) and (time() < $KO_fin_time22)) {
            $komlinks[] = array(
                "start" => $KO_start_time22,
                "fin" => $KO_fin_time22,
                "img" => $KO_A_IMG22,
                "title" => $KO_A_TITLE22,
                "text" => $KO_A_TXT22,
                "url" => $KO_A_URL22,

            );
        }

        if ((time() > $KO_start_time21) and (time() < $KO_fin_time21)) {
            $komlinks[] = array(
                "start" => $KO_start_time21,
                "fin" => $KO_fin_time21,
                "img" => $KO_A_IMG21,
                "title" => $KO_A_TITLE21,
                "text" => $KO_A_TXT21,
                "url" => $KO_A_URL21,

            );
        }

        if ((time() > $KO_start_time55) and (time() < $KO_fin_time55)) {
            $komlinks[] = array(
                "start" => $KO_start_time55,
                "fin" => $KO_fin_time55,
                "img" => $KO_A_IMG55,
                "title" => $KO_A_TITLE55,
                "text" => $KO_A_TXT55,
                "url" => $KO_A_URL55,
                "color" => "#1103CD",
                "color2" => "#0D084A",
            );
        }

        if ((time() > $KO_start_time43) and (time() < $KO_fin_time43)) {
            $komlinks[] = array(
                "start" => $KO_start_time43,
                "fin" => $KO_fin_time43,
                "img" => $KO_A_IMG43,
                "title" => $KO_A_TITLE43,
                "text" => $KO_A_TXT43,
                "url" => $KO_A_URL43);

        }

        if ((time() > $ny_events['skupkastart']) and (time() < $ny_events['skupkaend'])) {
            // скупка
            $komlinks[] = array(
                "start" => $ny_events['skupkastart'],
                "fin" => $ny_events['skupkaend'],
                "img" => "http://i.oldbk.com/i/action/slider_2b.jpg",
                "title" => 'Акция "Скупка"',
                "text" => 'Только два дня! Государственный магазин скупает ненужные вещи по высокой цене!',
            );

        }

        if ((time() > $KO_start_time19) and (time() < $KO_fin_time19)) {
            $komlinks[] = array(
                "start" => $KO_start_time19,
                "fin" => $KO_fin_time19,
                "img" => $KO_A_IMG19,
                "title" => $KO_A_TITLE19,
                "text" => $KO_A_TXT19,
                "url" => $KO_A_URL19);

        }

        if ((time() > $KO_start_time17) and (time() < $KO_fin_time17)) {
            $komlinks[] = array(
                "start" => $KO_start_time17,
                "fin" => $KO_fin_time17,
                "img" => $KO_A_IMG17,
                "title" => $KO_A_TITLE17,
                "text" => $KO_A_TXT17,
                "url" => $KO_A_URL17);

        }

        if (time() > $KO_start_time3 && time() < $KO_fin_time3) {
            //лето
            $komlinks[] = array(
                "start" => $KO_start_time3,
                "fin" => $KO_fin_time3,
                "img" => $KO_A_IMG3,
                "title" => $KO_A_TITLE3,
                "text" => $KO_A_TXT3,
                "url" => $KO_A_URL3);
        }

        if ((time() > $KO_start_time20) and (time() < $KO_fin_time20)) {
            $komlinks[] = array(
                "start" => $KO_start_time20,
                "fin" => $KO_fin_time20,
                "img" => $KO_A_IMG20,
                "title" => $KO_A_TITLE20,
                "text" => $KO_A_TXT20,
                "url" => $KO_A_URL20);

        }



        if (time() > $start_volna && time() < $end_volna) {
            //образы и картинки
            $komlinks[] = array(
                "start" => $start_volna,
                "fin" => $end_volna,
                "img" => $VOLNA_IMG,
                "title" => $VOLNA_TITLE,
                "text" => $VOLNA_TXT,
                "url" => $VOLNA_URL);
        }
        //надо добавлять картинки на всякие волны хаоса

        if (time() > $ny_events['elkacpeatstart'] && time() < $ny_events['elkacpeatend']) {
            $komlinks[] = array(
                "start" => $ny_events['elkacpeatstart'],
                "fin" => $ny_events['elkacpeatend'],
                "img" => "http://i.oldbk.com/i/action/slider_sized_winter6.jpg",
                "title" => '"С новым 2018 годом!"',
                "text" => 'Подарок под елкой и различные акции и сюрпризы в новогодние дни в ОлдБК!',
            );
        }

        /*
                    $komlinks[] =  array(
                            "img" => "http://i.oldbk.com/i/action/runes.jpg",
                            "title" => '"Руны"',
                            "text" => 'Прокачай руны боевой магией и сделай своего бойца значительно сильнее!',
                            "url" => "http://oldbk.com/encicl/?/runes_info.html");
*/
        /*
        $komlinks[] =  array(
        "img" => "http://i.oldbk.com/i/action/3fight.jpg",
        "title" => '"Трехсторонний бой"',
        "text" => 'Попробуй свои силы в уникальной системе боя для 3х склонностей и собери черепа врагов.',
        "url" => "http://oldbk.com/encicl/?/3dboi.html");
        */
        /*
        $komlinks[] = array(
        "img" => "http://i.oldbk.com/i/action/ruins.jpg",
        "title" => 'Локация "Руины"',
        "text" => 'Командная тактика и стратегия, азарт и адреналин! Дойди до конца и забери сокровища.',
        "url" => "http://oldbk.com/encicl/?/ruins.html");
        */

        /*$komlinks[] = array(
                "img" => "http://i.oldbk.com/i/action/castles.jpg",
                "title" => 'Локация "Замки"',
                "text" => "Сплоченность клана проявится в борьбе. Награда - уникальные абилити и магические книги.",
                "url" => "http://oldbk.com/encicl/?/zamki.html");*/

        $komlinks[] = array(
            "start" => mktime(23, 59, 59, 1, 1, 2016),
            "fin" => mktime(23, 59, 59, 1, 1, 2066),
            "img" => $KO_A_IMG13,
            "title" => $KO_A_TITLE13,
            "text" => $KO_A_TXT13,
        );

        function order_actions($arr)
        {
            $arr_out=array();
            $arr_ok=array();
            $nr=0;
            foreach($arr as $k => $vline)
            {
                foreach($vline as $nazv=> $val)
                {
                    if ($nazv=='start')
                    {
                        $nr++; // +1 секунда для избежания пропаданий акций с одинаковым стартом
                        if ($val=='') $val=time();
                        $arr_out[$val+$nr]=$vline;
                    }
                }
            }

            krsort($arr_out); //сортируем

            //возвращаем ид на место
            foreach($arr_out as $k => $vline)
            {
                $arr_ok[]=$vline;
            }

            return $arr_ok;
        }

        return order_actions($komlinks);
    }


    /**
     * @param $action
     * @return bool|void
     */
    protected function beforeAction($action)
    {
        $this->registerCssAndScripts();

        return parent::beforeAction($action);
    }

    /**
     *
     */
    protected function registerCssAndScripts()
    {
        $this->app->clientScript
            ->registerCssFile('/assets/adaptive/css/bootstrap.min.css')
            ->registerCssFile('/assets/adaptive/css/font-awesome.min.css')
            ->registerCssFile('/assets/adaptive/css/kp-new-style.css')
            ->registerCssFile('/assets/adaptive/css/img.min.css')
            ->registerCssFile('/assets/shadowbox/css/shadowbox.css')
            ->registerCssFile('/assets/home/css/home.css');


        $this->app->clientScript
            ->registerJsFile('/js/advert.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('/assets/jquery/jquery.min.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('/js/gatracking/gat.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/home/js/bootstrap.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/shadowbox/js/shadowbox.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/home/js/js.main.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/js/qa.js', ClientScript::JS_POSITION_END);
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