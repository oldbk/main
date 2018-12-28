<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;

/**
 * Class DressroomCompl
 * @package components\Model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $code
 * @property string $title
 * @property string $link
 * @property integer $status
 * @property integer $created_at
 */
class DressroomCompl extends AbstractCapitalModel
{
    /**
     * @param string $className
     * @return Magic
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function fieldMap()
    {
        return array(
            'id', 'user_id', 'link', 'code', 'title', 'status', 'created_at'
        );
    }

    public static function tableName()
    {
        return 'dressroom_compl';
    }

    public static function pkField()
    {
        return 'id';
    }

    public function getPk()
    {
        return $this->id;
    }
}