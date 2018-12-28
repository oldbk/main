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
 * @property int $id
 * @property int $type
 * @property string $topic
 * @property string $text
 * @property string $date
 * @property int $parent
 * @property int $author
 * @property string $a_info
 * @property string $min_align
 * @property string $max_align
 * @property float $fix
 * @property string $updated
 * @property int $closepal
 * @property string $close_info
 * @property int $icon
 * @property int $del_top
 * @property int $delpal
 * @property string $del_info
 * @property int $deltopal
 * @property string $deltop_info
 * @property int $ok
 * @property string $pal_comments
 * @property int $vote
 * @property int $only_own
 * @property int $is_test
 * @property int $is_closed
 *
 *
 */
class Forum extends AbstractCapitalModel
{
    /**
     * @param string $className
     * @return Forum
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function tableName()
    {
        return 'forum';
    }

    public static function pkField()
    {
        return 'id';
    }

    public function getPk()
    {
        return $this->id;
    }

    protected function fieldMap()
    {
        return array(
            'id', 'type', 'topic', 'text', 'date', 'parent', 'author', 'a_info', 'min_align', 'max_align',
            'fix', 'close', 'updated', 'closepal', 'close_info', 'icon', 'del_top', 'delpal', 'del_info',
            'deltopal', 'deltop_info', 'ok', 'pal_comment', 'vote', 'only_own', 'is_closed'
        );
    }
}