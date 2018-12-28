<?php
namespace components\Controller;
use components\Controller\_base\ForumController;
use components\Model\Forum;
use components\Model\ForumLike;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.10.2015
 */
class LikeController extends ForumController
{
    /**
     * Ставим лайк
     */
    public function likeAction()
    {
        try {
            if(!($topic = $this->get('request')->get('topic'))) {
                throw new \Exception('Топик не найден');
            }
            $topic = Forum::find('id = ? and topic != ""', array($topic))->asArray();
            if(!$topic) {
                throw new \Exception('Топик не найден');
            }
            $like = ForumLike::find('topic = ? and user_id = ?', array($topic['id'], $this->user['id']), array('is_deleted'))->asArray();
            if(!$like) {
                $_data = array(
                    'topic' => $topic['id'],
                    'user_id' => $this->user['id'],
                    'created_at' => time(),
                );
                if(!ForumLike::insert($_data)) {
                    throw new \Exception('');
                }
            } elseif($like['is_deleted']) {
                $_data = array(
                    'is_deleted' => 0,
                    'updated_at' => time(),
                );
                if(!ForumLike::update($_data, 'topic = ? and user_id = ?', array($topic['id'], $this->user['id']))) {
                    throw new \Exception('');
                }
            } else {
                throw new \Exception('Вы уже ставили like для этого топика');
            }

            $this->renderJSON(array(
                'status' => 'ok',
                'count' => $this->getCurrentLikeCount($topic['id']),
            ));
        } catch (\Exception $ex) {
            $this->renderJSON(array(
                'status' => 'error',
                'message' => $ex->getMessage()
            ));
        }
    }

    /**
     * Удаляем лайк
     */
    public function removeAction()
    {
        try {
            if(!($topic = $this->get('request')->get('topic'))) {
                throw new \Exception('Топик не найден');
            }
            $topic = Forum::find('id = ? and topic != ""', array($topic))->asArray();
            if(!$topic) {
                throw new \Exception('Топик не найден');
            }
            $like = ForumLike::find('topic = ? and user_id = ?', array($topic['id'], $this->user['id']), array('is_deleted'))->asArray();
            if($like || $like['is_deleted'] == 0) {
                $_data = array(
                    'is_deleted' => 1,
                    'updated_at' => time(),
                );
                ForumLike::update($_data, 'topic = ? and user_id = ?', array($topic['id'], $this->user['id']));
            } else {
                throw new \Exception('Вы уже удаляли like для этого топика');
            }

            $this->renderJSON(array(
                'status' => 'ok',
                'count' => $this->getCurrentLikeCount($topic['id']),
            ));
        } catch (\Exception $ex) {
            $this->renderJSON(array(
                'status' => 'error',
                'message' => $ex->getMessage()
            ));
        }
    }

    private function getCurrentLikeCount($id)
    {
        return ForumLike::count('topic = ? and is_deleted = 0', array($id));
    }
}