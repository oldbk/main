<?php


namespace components\Controller\forum;


use Carbon\Carbon;
use components\Eloquent\Forum;
use components\Eloquent\User;
use components\Exceptions\ForumException;
use components\Helper\Rvs;
use components\Helper\Str;

class PostController extends ForumController
{
    /**
     * @param $id
     *
     * Создание поста в топике
     */
    public function createPostAction($id)
    {
        //объект запроса
        $request = $this->app->request;
        //$_POST данные
        $post_data = $request->post();
        $text = $post_data['text'];

        try {

            if (!$this->user) {
                throw new ForumException('Ку ку');
            }

            //проверка токена пришедшего из формы
//            $this->checkToken();

            //защита от слов паразитов спамеров =)) ( и если запрос сделан аяксом в том числе !)
            $rvs = Rvs::detect($post_data, [
                'text' => [
                    'contains',
                    'rvs_links',
                ],
            ], true);

            if ($rvs !== false) {
                $text = '/?RVS';
            }

            if ($this->app->session->get('captcha_data')['show'] && !\components\Helper\Captcha::validate()) {
                throw new ForumException('Неверный защитный код!');
            }


            if ($this->spamDetect()) {
                throw new ForumException('Не стоит спамить...');
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
                ['text' => $text],
                [
                    'text' => [
                        'required',
                        'forum_rvs',
                    ],
                ],
                [
                    'text.required' => 'Текст не может быть пустым',
                    'text.forum_rvs' => 'РВС',
                ]
            );

            if ($validator->fails()) {
                throw new ForumException($validator->errors()->first());
            }

            $text = strip_tags(nl2br(htmlspecialchars_decode(trim($text))), '<b><i><u><code><br><blockquote>');
            $text = Str::closetags($text);
            $text = Str::stripTagAttributes($text);
            $text = Str::makeLink($text);
//            $text = \Xss::clean($text);

            if (trim(strip_tags($text)) == '') {
                throw new ForumException('Запрещенные символы');
            }


            //топ
            $topic = Forum::find($id);

            //существует ли топ или скрыт от юзеров
            if (!$topic || (!$this->user->isAdmin() && !$this->user->isPaladin() && $topic->isDeleted('top'))) {
                return $this->app->redirectWithError('Тема удалена с форума, либо её не существует', $this->app->urlFor('forum'));
            }

            //разрешенные конфы для текущего юзера
            $categories = $this->getCategories();
            //есть ли у юзера право доступа к конфе
            $conf_query = $categories->where('id', '=', $topic->parent);

            if (!$this->user->isAdmin() && !$this->user->isAdminion()) {
                $conf_query = $conf_query
                    ->where('min_level', '<=', $this->user->level)
                    ->where('max_level', '>=', $this->user->level);
            }

            $conf = $conf_query->first();

            if (!$conf) {
                throw new ForumException('Не надо так делать!');
            }

            //топ уже закрыт
            if ($topic->isClosed() && (!$this->user->isAdmin() && !$this->user->isPaladin())) {
                throw new ForumException('Тема закрыта для обсуждений');
            }

            //хаос и нет права писать в конфе с хаосом
            if ($this->user->hasChaos() && !$topic->canCreatePostWithChaos()) {
                throw new ForumException('Хаосникам запрещено писать на форуме');
            }

            //молчанка и игнорим молчанку в конфе нужной (hardcode)
            if ($this->user->hasForumSilence() && !$conf->canCreateWithSilens($this->user)) {
                throw new ForumException('На персонаже заклятие форумного молчания');
            }

            $hidden = $this->user->isAdmin() || $this->user->isAdminion() || $this->user->isHighPaladin()
                ? $this->user->getAttribute('hidden')
                : 0;

            //все гуд, пишем новый пост
            $new_post = Forum::create([
                'type' => 2,
                'text' => $text,
                'parent' => $topic->id,
                'min_align' => $topic->min_align,
                'max_align' => $topic->max_align,
                'author' => $this->user->id,
                'date' => Carbon::now()->format('d.m.y H:i:s'),
                'a_info' => [
                    $this->user->login,
                    $this->user->klan,
                    $this->user->align,
                    $this->user->level,
                    $hidden,
                ]
            ]);

            //что-то пошло не так (
            if (!$new_post) {
                throw new ForumException('Ошибка создания поста');
            }

            //добавляем в индекс новый пост
            $new_post->addToIndex(['text']);

            $update_data = [
                'updated' => Carbon::now()
            ];

            //закрываем топ если стояла галочка
            if (isset($post_data['andclose']) && ($this->user->isAdmin() || $this->user->isAdminion() || $this->user->isHighPaladin())) {

                $update_data = array_merge($update_data, [
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

            }

            //обновляем топ
            $topic->update($update_data);

            //Устанавливаем куку с временем последнего сообщения
            $this->setLastMessageTime();

            //редиректим обратно в тему
            $this->app->redirect($this->app->request->getReferer() . '#p' . $new_post->id);

        } catch (ForumException $exception) {

            //запоминаем введенный текст сообщения
            $this->app->flash('text', $post_data['text']);

            //редиректим назад
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $params
     *
     * скрыть/удалить пост в топике
     */
    public function deletePostAction($params)
    {
        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->isForumModerator()) {
                throw new ForumException('Нет прав модератора');
            }

            if (!$this->user->canPostDelete()) {
                throw new ForumException('Нет прав удаления постов');
            }

            $get_topic = $params[0];
            $get_post = $params[1];

            $post = Forum::find($get_post);

            if (!$post) {
                throw new ForumException('Пост не найден');
            }

            //удалить из базы
            if ($request->get('hard')) {

                if ($post->isMain()) {
                    throw new ForumException('Этот пост не может быть удален из базы, для удаления топика выбрать соответствующее меню');
                }

                if (!$this->user->canPostDelete('hard')) {
                    throw new ForumException('Нет прав');
                }

                //Удаляем из базы
                $post->delete();

                //Удаляем из индекса
                $post->deleteFromIndex();

                $this->app->flash('noty', [
                    'type' => 'success',
                    'msg' => 'Пост удален из базы',
                ]);

                return $this->app->redirect($request->getReferer());
            }

            $post->delpal = $this->user->id;
            $post->del_info = [
                $this->user->login,
                $this->user->klan,
                $this->user->align,
                $this->user->level,
                $this->user->getAttribute('hidden'),
            ];

            //Причина удаления (ака коммент красным)
            if ($request->post('delete_post_reason') != '') {
                $reason = strip_tags($request->post('delete_post_reason'), '<a>');

                $pal_comments = $post->pal_comments ?: [];

                $comment_author = false;

                if ($a = $request->post('reason_author')) {

                    $ra = array_shift($a);

                    if($ra == -1 && $this->user->canBeInvisible()) {//Невидимка
                        $comment_author = [
                            null,
                            null,
                            0,
                            null,
                            -1,
                            $this->user->id
                        ];
                    } else {
                        throw new ForumException('Хм...А вам нельзя писать из под невидимки!');
                    }

                }

                array_push($pal_comments, [
                    'author' => $comment_author,
                    'text' => '<small>(' . Carbon::now()->format('d.m.y H:i:s') . ')</small> ' . $reason
                ]);

                $post->pal_comments = array_values($pal_comments);
            }

            $post->save();

//            if ($post->appeal) {
//                $post->appeal->delete();
//            }

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Пост удален',
            ]);

            $this->app->redirect($request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $params
     *
     * Восстановление поста в топике
     */
    public function restorePostAction($params)
    {
        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->isForumModerator()) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canPostRestore()) {
                throw new ForumException('Нет прав');
            }

            $get_topic = $params[0];
            $get_post = $params[1];

            $post = Forum::find($get_post);

            if (!$post) {
                throw new ForumException('Пост не найден');
            }

            $post->update([
                'delpal' => null,
                'del_info' => null,
            ]);

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Пост восстановлен',
            ]);

            $this->app->redirect($request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $params
     *
     * Редактирование поста
     */
    public function editPostAction($params)
    {
        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canEditPost()) {
                throw new ForumException('Нет прав');
            }

            $post = Forum::where('id', $params[1])->first();

            if(!$post) {
                throw new ForumException('Пост не найден');
            }

            if($request->isGet()) {

                $text = strip_tags(trim($post->text), '<b><i><u><code><blockquote><div><img>');

                return $this->renderJSON([
                    'status' => 1,
                    'text' => $text,
                    'type' => 'success',
                ]);
            }

            $text = strip_tags(nl2br(htmlspecialchars_decode(trim($request->post('edit_post')))), '<b><i><u><code><br><blockquote><div><img>');
            $text = Str::closetags($text);
            $text = Str::stripTagAttributes($text);
            $text = Str::makeLink($text);
//            $text = \Xss::clean($text);

            //сохраняем пост
            $post->text = $text;
            $post->save();

            try {
                //апдейтим индекс
                $post->updateIndex(['text']);
            } catch (\Exception $exception) {

            }

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Пост отредактирован',
            ]);

            $this->app->redirect($request->getReferer());

        } catch (ForumException $exception) {
            //редиректим назад
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $params
     *
     * Добавление коммента
     */
    public function addCommentAction($params)
    {

        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canCommentWrite()) {
                throw new ForumException('Не надо так делать!');
            }

            $get_topic = $params[0];
            $get_post = $params[1];
            $comment = strip_tags(nl2br(htmlspecialchars_decode($request->post('comment'))), '<a><br>');
            $comment = Str::closetags($comment);
            $comment = Str::stripTagAttributes($comment);
            $comment = Str::makeLink($comment);

            $post = Forum::find($get_post);

            if (!$post) {
                throw new ForumException('Пост не найден');
            }

            $pal_comments = $post->pal_comments ?: [];

            $c_a = $request->post('comment_author');

            $comment_author = [
                $this->user->login,
                $this->user->klan,
                $this->user->align,
                $this->user->level,
                (($c_a && is_array($c_a) && !empty($c_a)) ? array_shift($c_a): $this->user->getAttribute('hidden')),
                $this->user->id,
            ];

            array_push($pal_comments, [
                'author' => $comment_author,
                'text' => '<small>(' . Carbon::now()->format('d.m.y H:i:s') . ')</small> ' . $comment
            ]);

            $post->pal_comments = array_values($pal_comments);

            $post->save();

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Комментарий добавлен',
                'id' => $post->id,
            ]);

            $this->app->redirect($request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }
    }

    /**
     * @param $params
     *
     * Удаление комментов (красненьким =))
     */
    public function removeCommentAction($params)
    {

        $request = $this->app->request;

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }


            if (!$this->user->canCommentDelete()) {
                throw new ForumException('Нет прав');
            }

            $get_topic = $params[0];
            $get_post = $params[1];
            $comment_number = $this->app->request->get('comment_number');

            $post = Forum::find($get_post);

            if (!$post) {
                throw new ForumException('Пост не найден');
            }

            $pal_comments = $post->pal_comments;

            unset($pal_comments[$comment_number]);

            $post->pal_comments = !empty($pal_comments) ? array_values($pal_comments) : null;
            $post->save();

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Комментарий удален',
                'id' => $post->id,
            ]);

            $this->app->redirect($request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }
}