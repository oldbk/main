<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;

/**
 * Class Magic
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $name
 * @property int $chanse
 * @property int $time
 * @property string $file
 * @property int $targeted
 * @property string $img
 * @property int $battle_use
 * @property int $need_block
 * @property int $nlevel
 *
 */
class Magic extends AbstractCapitalModel
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
            'id', 'name', 'chanse', 'time', 'file', 'targeted', 'img', 'battle_use', 'need_block', 'nlevel'
        );
    }

    public static function tableName()
    {
        return 'magic';
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