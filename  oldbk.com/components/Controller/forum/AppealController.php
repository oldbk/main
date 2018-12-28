<?php

namespace components\Controller\forum;


use components\Eloquent\Chat;
use components\Eloquent\Forum;
use components\Eloquent\ForumAppeal;
use components\Exceptions\ForumException;

/**
 * Class AppealController
 * @package components\Controller\forum
 */
class AppealController extends ForumController
{
    /**
     * @param $params
     */
    public function appealPostAction($params)
    {

        try {

            if (!$this->user) {
                throw new ForumException('Для подачи жалобы войдите в игру');
            }

            $get_topic = intval($params[0]);
            $get_post = intval($params[1]);

            $topic = Forum::find($get_topic);

            if (!$topic) {
                throw new ForumException('Топ не найден');
            }

            $post = $topic->id == $get_post
                ? $topic
                : $topic->children()->find($get_post);

            if (!$post) {
                throw new ForumException('Пост не найден');
            }


            $appeal = ForumAppeal::withTrashed()
                ->firstOrCreate(
                    [
                        'post_id' => $post->id,
                    ],
                    [
                        'top_id' => $topic->id,
                        'author_id' => $post->author,
                        'user_id' => $this->user->id,
                    ]
                );

            if (!$appeal->wasRecentlyCreated) {
                throw new ForumException('На это сообщение уже поступила жалоба!');
            }

            $this->sendAppealToPalChat($appeal);

            $this->renderJSON([
                'status' => 1,
                'text' => 'Жалоба отправлена',
                'type' => 'success',
            ]);


        } catch (ForumException $exception) {

            $this->renderJSON([
                'status' => 0,
                'text' => $exception->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * @param $appeal
     */
    public function sendAppealToPalChat($appeal)
    {

        $reporter = $appeal->reporter()->first(['login']);
        $violator = $appeal->violator()->first(['login']);
        $topic = $appeal->topic()->first();
        $post = $appeal->post()->first();

        $collection = $appeal->top_id !== $appeal->post_id
            ? collect($topic->children()->get(['id'])->toArray())
            : collect([$post->toArray()]);

        $pkey = $collection->search(function($item) use ($appeal) {
            return $item['id'] == $appeal->post_id;
        });

        $page = floor($pkey / 20) + 1;
        $text = ":[" . time() . "]:[Архивариус:|:83]:[private [klan-pal-4] Жалоба от <b>" . ($reporter->login ?? $appeal->user_id) . "</b> на: <span class=\"date2\">" . ($post->date) . "</span> <a href=javascript:top.AddTo(\"" . ($violator->login ?? $appeal->author_id) . "\")><span oncontextmenu=\"return OpenMenu(event,10)\"> <b>" . ($violator->login ?? $appeal->author_id) . "</b></span></a> в топике <a href=\"" . (\Config::get('url.oldbk') . $this->app->urlFor('forum_topic', ['id' => $appeal->top_id])) . "?page=".$page."#".$appeal->post_id."\" target=\"_blank\">" . $topic->topic . "</a>]:[" . $this->user->room . "]";

        Chat::create([
            'text' => $text,
            'city' => 1,
        ]);

    }
}