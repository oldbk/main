<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $topic
 * @property int $user_id
 * @property int $is_deleted
 * @property int $updated_at
 * @property int $created_at
 *
 *
 */
class ForumLike extends AbstractCapitalModel
{
    /**
     * @param string $className
     * @return ForumLike
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function tableName()
    {
        return 'forum_like';
    }

    public static function pkField()
    {
        return false;
    }

    public function getPk()
    {
        return false;
    }

    protected function fieldMap()
    {
        return array(
            'topic', 'user_id', 'is_deleted', 'updated_at', 'created_at'
        );
    }
}