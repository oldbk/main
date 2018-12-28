<?php


namespace components\Controller\forum;


use components\Eloquent\Forum;
use components\Eloquent\ForumLike;
use components\Exceptions\ForumException;

class LikeController extends ForumController
{
    /**
     * @param $id
     *
     * Добавление лайка
     */
    public function addLikeAction($id)
    {
        try {

            $topic = Forum::where('id', $id)
                ->where('topic', '!=', "")
                ->first();


            if(!$topic) {
                throw new ForumException('Топик не найден');
            }

            $like = $topic->likes()
                ->where('user_id', $this->user->id)
                ->first();


            if(is_null($like)) {
                $_data = array(
                    'topic' => $topic->id,
                    'user_id' => $this->user->id,
                    'created_at' => time(),
                );

                ForumLike::create($_data);

            } elseif($like['is_deleted']) {
                $_data = array(
                    'is_deleted' => 0,
                    'updated_at' => time(),
                );

                $topic->likes()->where('user_id', $this->user->id)->update($_data);

            } else {
                throw new ForumException('Вы уже ставили like для этого топика');
            }

            $count = $topic->likes()
                ->where('is_deleted', '=', 0)
                ->count();

            $this->renderJSON(array(
                'status' => 'ok',
                'count' => $count,
            ));

        } catch (ForumException $ex) {
            $this->renderJSON(array(
                'status' => 'error',
                'message' => $ex->getMessage()
            ));
        }
    }

    /**
     * @param $id
     *
     * Удаление лайка
     */
    public function removeLikeAction($id)
    {
        try {

            $topic = Forum::where('id', $id)
                ->where('topic', '!=', "")
                ->first();

            if(!$topic) {
                throw new ForumException('Топик не найден');
            }

            $like = $topic->likes()
                ->where('user_id', $this->user->id)
                ->first();


            if($like || $like['is_deleted'] == 0) {
                $_data = array(
                    'is_deleted' => 1,
                    'updated_at' => time(),
                );

                $topic->likes()->where('user_id', $this->user->id)->update($_data);

            } else {
                throw new ForumException('Вы уже удаляли like для этого топика');
            }

            $count = $topic->likes()
                ->where('is_deleted', '=', 0)
                ->count();

            $this->renderJSON(array(
                'status' => 'ok',
                'count' => $count,
            ));
        } catch (ForumException $ex) {
            $this->renderJSON(array(
                'status' => 'error',
                'message' => $ex->getMessage()
            ));
        }
    }
}