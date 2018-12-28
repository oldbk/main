<?php

namespace components\Controller\forum;


use Carbon\Carbon;
use components\Eloquent\Forum;
use components\Enum\UserAlign;
use components\Exceptions\ForumException;
use components\Helper\AuthorInfo;
use components\Helper\Rvs;
use components\Helper\Str;

/**
 * Class TopController
 * @package components\Controller\forum
 */
class TopController extends ForumController
{
    /**
     * @param null $confId
     * @throws \Exception
     */
    public function indexAction($confId = null)
    {
        $this->confAction($confId);
    }

    /**
     * @param null $confId
     * @return string
     * @throws \Exception
     */
    public function confAction($confId = null)
    {
        $request = $this->get('request');

        $confId = $confId ?: 1;

        //получаем категории разрешенные для юзера
        $categories = $this->getCategories($confId);

        $this->createToken();

        $this->app->view()->appendLayoutData([
            'categories' => $categories,
        ]);

        try {

            //разрешена ли текущая конфа к просмотру
            $conf = $categories->find($confId);
            if (!$conf) {
                throw new ForumException(\Lang::get('forum.conf_access_deny'));
            }

            //получаем список топиков с лайками и количеством ответов в топике и тд..
            $query_topics = $conf->children()
                ->with('children')
                ->with(['likes' => function ($q) {
                    $q->where('is_deleted', 0);
                    $q->where('user_id', $this->user['id']);
                }])
                ->withCount(['likes' => function ($q) {
                    $q->where('is_deleted', 0);
                }]);


            if ($this->user && !$this->user->isAdmin() && !$this->user->isHighPaladin()) {

                if ($conf['only_own'] && $conf['id'] != 230437581) {
                    $query_topics->where('author', $this->user->id);

                    if ($conf['id'] == 47) {
                        $query_topics->orWhere(function ($q) {
                            return $q->where('parent', 47)->where('id', 230437581);
                        });
                    }

                }
            }

            //убираем с выборки удаленные(скрытые) топы
            if (!$this->user || ($this->user && !$this->user->canSeeDeletedTop())) {
                $query_topics->where(function ($q) {
                    $q->whereNull('deltoppal');
                    $q->orWhere('deltoppal', '=', 0);

                    //если пал и он удалял этот топ, то показываем его
                    if ($this->user && $this->user->isPaladin()) {
                        $q->orWhere('deltoppal', '=', $this->user->id);
                    }
                });
            }


            //вывод по 20 топов на страницу
            $topics = $query_topics
                ->orderBy('fix', 'desc')
                ->orderBy('updated', 'desc')
                ->paginate(20, ['*'], 'page', $request->get('page'));


            //формируем данные, последние 10 ответов в теме,  скрываем невидимок
            $topics->getCollection()->map(function (Forum $topic) {

                $last_authors = [];
                foreach ($topic->children->take(-10) as $post) {

                    $last_authors[] = $post->a_info[4] > 0
                        ? '<i>Невидимка</i>'
                        : $post->a_info[0];

                }
                $topic['last_authors'] = $last_authors;

                $topic['post_author'] = AuthorInfo::buildForPostAuthor($topic->author, $topic->a_info, $this->user);

                return $topic;
            });

            //аппендим гет параметры к урлу
            $topics->appends($request->get());

//            $this->createToken();

            //рендерим список
            return $this->render('forum/topics', [
                'conf' => $conf,
                'topics' => $topics,
                'elements' => $this->makeElements($topics),
            ]);

        } catch (ForumException $exception) {

            //Записываем эроры для отображения
            $this->app->flashNow('errors', [
                $exception->getMessage(),
            ]);

            return $this->render('forum/notfound');
        }


    }

    /**
     * @param $id
     * @return string
     * @throws \Exception
     */
    public function topicAction($id)
    {

        $request = $this->get('request');

        //получаем категории разрешенные для юзера
        $categories = $this->getCategories();

        $this->createToken();

        $this->app->view()->appendLayoutData([
            'categories' => $categories,
        ]);

        try {

            //топик стартер
			/** @var Forum $main_post */
            $main_post = Forum::where('id', $id)
                ->with('above')
                ->with(['likes' => function ($q) {
                    $q->where('is_deleted', 0);
                    $q->where('user_id', $this->user['id']);
                }])
                ->withCount(['likes' => function ($q) {
                    $q->where('is_deleted', 0);
                }])
                ->first();

            if (!$main_post) {
                throw new ForumException(\Lang::get('forum.topic_deleted'));
            }

            //если пост не является топик стартером
            if ($main_post->notMain()) {
                throw new ForumException(\Lang::get('forum.topic_not_main'));
            }

            //нельзя смотреть топы в этой конфе
            $conf = $categories->where('id', $main_post->parent)->first();
            if (!$conf) {
                throw new ForumException(\Lang::get('forum.topic_access_deny'));
            }

            //топик удален и запрет на просмотр топика
            if (
                $main_post->isDeleted('top') &&
                (!$this->user || ($this->user && !$main_post->canSeeDeleted($this->user))))
            {
                throw new ForumException(\Lang::get('forum.topic_deleted'));
            }

            if (
                $this->user &&
                !$this->user->isAdmin() &&
                !$this->user->isAdminion() &&
                !$this->user->isHighPaladin() &&
                $main_post->author !== $this->user->id &&
                ($conf->only_own && $main_post->id != 230437581)
            ) {
                throw new ForumException(\Lang::get('forum.topic_deleted'));
            }


            //посты в топике
            $children_posts = $main_post->children()
                ->orderBy('updated', 'asc')
                ->paginate(20, ['*'], 'page', $request->get('page'));

            //добавляем топик стартера в результат
            $children_posts->prepend($main_post);
            //прячем невидимок
            $children_posts->getCollection()->map(function (Forum $post) {

                $post['post_author'] = AuthorInfo::buildForPostAuthor($post->author, $post->a_info, $this->user);

                if ($post->isDeleted('post')) {
                    $moderator_info = AuthorInfo::buildForModerator($post->getModeratorInfo('post'), $this->user);
                    $post['deleted_text'] = 'Удалено ' . $this->renderPartial('common/rendermoderator', ['user' => $moderator_info], true);
                }

                return $post;
            });

            $children_posts->appends($request->get());

            $prevTopic = $conf
                ? $main_post->where('id', '>', $main_post->id)->where('parent', $main_post->parent)->orderBy('id', 'asc')->first()
                : false;
            $nextTopic = $conf
                ? $main_post->where('id', '<', $main_post->id)->where('parent', $main_post->parent)->orderBy('id', 'desc')->first()
                : false;


            return $this->render('forum/posts', [
                'main_post' => $main_post,
                'children_posts' => $children_posts,
                'elements' => $this->makeElements($children_posts),
                'prevTopic' => $prevTopic,
                'nextTopic' => $nextTopic,
                'categories' => $categories,
            ]);

        } catch (ForumException $exception) {
            //Записываем эроры для отображения
            $this->app->flashNow('errors', [
                $exception->getMessage(),
            ]);

            return $this->render('forum/notfound');
        }

    }

    /**
     * @param $id
     *
     * Создание топика
     */
    public function createTopAction($id)
    {

        $request = $this->app->request;
        $post_data = $request->post();
        $title = $post_data['title'];
        $text = $post_data['text'];

        try {


            if (!$this->user) {
                throw new ForumException('User not set');
            }

            //проверка токена пришедшего из формы
//            $this->checkToken();

            $rvs = Rvs::detect($post_data, [
                'text' => [
                    'contains',
                    'rvs_links',
                ],
                'title' => [
                    'rvs_links',
                ]
            ], true);

            if (is_string($rvs)) {
                // $$rvs  -  string field text / title
                $$rvs = '/?RVS';
            }

            \Validator::extend(
                'forum_rvs',
                function ($attribute, $value, $parameters)
                {
                    return $value !== '/?RVS';
                }
            );

            //Валидация входящих данных
            $validator = \Validator::make(
                [
                    'title' => $title,
                    'text' => $text
                ],
                [
                    'title' => [
                        'required',
                        'forum_rvs',
                        'max:65',
                    ],
                    'text' => [
                        'required',
                        'forum_rvs',
                    ],
                ],
                [
                    'title.required' => 'Заголовок не может быть пустым',
                    'title.max' => 'Максимальная длина заголовка 65 символов',
                    'text.required' => 'Текст не может быть пустым',
                    'title.forum_rvs' => 'rvs.title',
                    'text.forum_rvs' => 'rvs.text',
                ]
            );

            if ($validator->fails()) {
                throw new ForumException($validator->errors()->first());
            }


            if ($this->app->session->get('captcha_data')['show'] && !\components\Helper\Captcha::validate()) {
                throw new ForumException('Неверный защитный код!');
            }


            if ($this->spamDetect()) {
                throw new ForumException('Не стоит спамить...');
            }

            //разрешенные конфы для текущего юзера
            $categories = $this->getCategories();
            //есть ли у юзера право доступа к конфе
            $conf_query = $categories->where('id', $id);

            if (!$this->user->isAdmin() && !$this->user->isAdminion()) {
                $conf_query = $conf_query
                    ->where('min_level', '<=', $this->user->level)
                    ->where('max_level', '>=', $this->user->level);
            }

            $conf = $conf_query->first();

            if (!$conf) {
                throw new ForumException('Не надо так делать!');
            }

            if ($this->user->hasChaos() && !$conf->canCreateTopWithChaos()) {
                throw new ForumException('Хаосникам запрещено писать на форуме');
            }

            if ($this->user->hasForumSilence() && !$conf->canCreateWithSilens($this->user)) {
                throw new ForumException('На персонаже заклятие форумного молчания');
            }

            if ($this->preventTopCreate($conf->id)) {
                throw new ForumException('Не стоит частить с написанием топиков...');
            }


            $title = Str::limit(strip_tags(htmlspecialchars_decode($post_data['title'])), 65);
//            $title = \Xss::clean($title);
            if (blank($title)) {
                throw new ForumException('Тема сообщения не может быть пустой');
            }

            $text = strip_tags(nl2br(htmlspecialchars_decode($post_data['text'])), '<b><i><u><code><br><blockquote>');
            $text = Str::closetags($text);
            $text = Str::stripTagAttributes($text);
            $text = Str::makeLink($text);
//            $text = \Xss::clean($text);


            if (blank(strip_tags($text))) {
                throw new ForumException('Запрещенные символы');
            }

            //иконка
            $icon = intval($post_data['icon']) < 14 ? intval($post_data['icon']) : 13;

            $hidden = $this->user->isAdmin() || $this->user->isAdminion() || $this->user->isHighPaladin()
                ? $this->user->getAttribute('hidden')
                : 0;

            $new_topic_data = [
                'topic'     => $title,
                'text'      => $text,
                'type'      => 2,
                'icon'      => $icon,
                'parent'    => $id,
                'min_align' => (int)$conf['min_align'],
                'max_align' => (int)$conf['max_align'],
                'author'    => $this->user->id,
                'date'      => Carbon::now()->format('d.m.y H:i:s'),
                'a_info'    => [
                    $this->user->login,
                    $this->user->klan,
                    $this->user->align,
                    $this->user->level,
                    $hidden,
                ]
            ];

            if ($request->post('close', false)) {
                if ($this->user->canTopClose()) {
                    $new_topic_data['close'] = 1;

                    if (!$request->post('close_anonym', false)) {
                        $new_topic_data['closepal'] = $this->user->id;
                        $new_topic_data['close_info'] = [
                            $this->user->login,
                            $this->user->klan,
                            $this->user->align,
                            $this->user->level,
                            $this->user->getAttribute('hidden'),
                        ];
                    }
                }
            }

            if ($request->post('fix', false)) {
                if ($this->user->canTopFix()) {
                    $new_topic_data['fix'] = ($this->user->isAdmin() || $this->user->isAdminion()) ? 2 : 1;
                }
            }


            if ($new_topic = Forum::create($new_topic_data)) {
                $new_topic->addToIndex(['text']);
            }


            if (!$new_topic) {
                throw new ForumException('Ошибка создания темы');
            }

            //Устанавливаем куку с временем последнего сообщения
            $this->setLastMessageTime();

            $this->app->redirectTo('forum_topic', ['id' => $new_topic->id]);

        } catch (ForumException $exception) {

            $this->app->flash('title', $post_data['title']);
            $this->app->flash('text', $post_data['text']);

            $this->app->redirectWithError($exception->getMessage());

        }

    }

    /**
     * @param $id
     *
     * Скрытие/удаление топика
     */
    public function deleteTopAction($id)
    {
        $request = $this->app->request;
        $hard = $request->get('hard', false);

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canTopDelete($hard)) {
                throw new ForumException('Нет прав');
            }


            $topic = Forum::find($id);

            if (!$topic) {
                throw new ForumException('Топик не найден');
            }

            if (!$hard && $topic->deltop_info) {
                throw new ForumException('Топ уже удален(скрыт) модератором: ' . $topic->deltop_info[0]);
            }

            if ($hard) {

                //Удаляем из индекса все посты в теме
                $topic->children->each(function ($p) {
                    $p->deleteFromIndex();
                });

                //удаляем все посты в теме
                $topic->children()->delete();

                //Удаляем из индекса топ
                $topic->deleteFromIndex();

                //удаляем сам топик
                $topic->delete();

                $this->app->flash('noty', [
                    'type' => 'success',
                    'msg' => 'Топ и все посты удалены из базы',
                ]);

//                return $this->app->redirect($request->getReferer());
                return $this->app->redirectTo('forum_conf', ['id' => $topic->parent]);
            }

            $topic->deltoppal = $this->user->id;
            $topic->deltop_info = [
                $this->user->login,
                $this->user->klan,
                $this->user->align,
                $this->user->level,
                $this->user->getAttribute('hidden'),
            ];

            if (!$topic->save()) {
                throw new ForumException('Ошибка скрытия топика');
            }

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Топ удален(скрыт)',
            ]);


            $this->app->redirect($request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $id
     *
     * Восстановение топика
     */
    public function restoreTopAction($id)
    {

        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canTopRestore()) {
                throw new ForumException('Нет прав');
            }

            $topic = Forum::find($id);

            if (!$topic) {
                throw new ForumException('Топ не найден');
            }

            if (!$topic->deltop_info) {
                throw new ForumException('Топ уже восстановлен модератором: ' . $topic->deltop_info[0]);
            }


            $topic->deltoppal = null;
            $topic->deltop_info = null;

            if (!$topic->save()) {
                throw new ForumException('Ошибка восстановления топика');
            }

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Топ восстановлен',
            ]);

            $this->app->redirect($request->getReferer());


        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $params
     *
     * Перенос топа
     */
    public function transferTopAction($params)
    {

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canTopMove()) {
                throw new ForumException('Нет прав');
            }

            $topic = Forum::find($params['top']);

            if (!$topic) {
                throw new ForumException('Топ не найден');
            }

            $close_info = [
                $this->user->login,
                $this->user->klan,
                $this->user->align,
                $this->user->level,
                $this->user->getAttribute('hidden'),
                1//метка, что топ закрыт по причине переноса
            ];


            //создаем новый топ со ссылкой на старый
            $new_topic_data = collect($topic)->except(['id'])->toArray();
            $new_topic_data['close'] = 1;
            $new_topic_data['pal_comments'] = null;
            $new_topic_data['closepal'] = $this->user->id;
            $new_topic_data['close_info'] = $close_info;
            $new_topic_data['text'] = '<br><a href="' . $this->app->urlFor('forum_topic', ['id' => $topic->id]) . '" target="_blank">Тема</a> <b style="color: red;">перенесена</b>';

            $new_topic = Forum::create($new_topic_data);


            //меняем родителя
            $topic->parent = $params['category'];
            $topic->save();


            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Тема перенесена в конференцию <b>' . $topic->above->topic . '</b>',
            ]);

            $this->app->redirectTo('forum_topic', ['id' => $new_topic->id]);

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $id
     *
     * Закрытие топа
     */
    public function closeTopAction($id)
    {

        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canTopClose()) {
                throw new ForumException('Нет прав');
            }

            $topic = Forum::find($id);

            if (!$topic) {
                throw new ForumException('Топ не найден');
            }

            if ($topic->close == 1) {
                throw new ForumException('Топ уже закрыт');
            }

            $topic->update([
                'close' => 1,
                'closepal' => $this->user->id,
                'close_info' => [
                    $this->user->login,
                    $this->user->klan,
                    $this->user->align,
                    $this->user->level,
                    $this->user->getAttribute('hidden'),
                ],
            ]);


            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Топ закрыт',
            ]);

            $this->app->redirect($request->getReferer());


        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }
    }

    /**
     * @param $id
     *
     * Открытие топа
     */
    public function openTopAction($id)
    {
        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canTopOpen()) {
                throw new ForumException('Нет прав');
            }


            $topic = Forum::find($id);

            if (!$topic) {
                throw new ForumException('Топ не найден');
            }

            if (!$topic->close) {
                throw new ForumException('Топ уже открыт');
            }

            $topic->update([
                'close' => 0,
                'closepal' => null,
                'close_info' => null,
            ]);

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Топ открыт',
            ]);

            $this->app->redirect($request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $id
     */
    public function fixTopAction($id)
    {
        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canTopFix()) {
                throw new ForumException('Нет прав');
            }

            $topic = Forum::find($id);

            if (!$topic) {
                throw new ForumException('Топ не найден');
            }

            $topic->update([
                'fix' => ($this->user->isAdmin() || $this->user->isAdminion()) ? 2 : 1
            ]);

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Топ закреплен',
            ]);

            $this->app->redirect($request->getReferer());


        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $id
     */
    public function unfixTopAction($id)
    {

        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canTopUnFix()) {
                throw new ForumException('Нет прав');
            }

            $topic = Forum::find($id);

            if (!$topic) {
                throw new ForumException('Топ не найден');
            }

            $topic->update([
                'fix' => null
            ]);

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Топ откреплен',
            ]);

            $this->app->redirect($request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }
    }

    /**
     * @param $id
     */
    public function deleteTopPostsAction($id)
    {
        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canTopDeletePosts()) {
                throw new ForumException('Нет прав');
            }

            $topic = Forum::find($id);

            if (!$topic) {
                throw new ForumException('Топ не найден');
            }


            //Удаляем из индекса все посты в топике
            $topic->children->each(function ($p) {
                $p->deleteFromIndex();
            });

            $topic->children()->delete();


            if ($request->get('close')) {
                $topic->close = 1;
                $topic->closepal = $this->user->id;
                $topic->close_info = [
                    $this->user->login,
                    $this->user->klan,
                    $this->user->align,
                    $this->user->level,
                    $this->user->getAttribute('hidden'),
                ];
                $topic->save();
            }

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Все посты в теме удалены',
            ]);

            $this->app->redirect($request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }
    }

    /**
     * @param $conf_id
     * @return bool
     */
    protected function preventTopCreate($conf_id)
    {

        $last_message = $this->user->posts()
            ->where('parent', $conf_id)
            ->where('parent', '<', 100)
            ->latest('id')
            ->first();

        if (!$last_message) {
            return false;
        }

        $diff = Carbon::createFromFormat('d.m.y H:i', $last_message->date)->diffInSeconds(Carbon::now());

        if ($conf_id == 47 && $diff < 3600) {
            return true;
        }

        if ($diff < 60) {
            return true;
        }

        return false;

    }
}