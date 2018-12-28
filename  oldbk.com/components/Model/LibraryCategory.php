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
 * @property string $title
 * @property int $is_enabled
 * @property int $order_position
 *
 */
class LibraryCategory extends AbstractCapitalModel
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
            'id', 'title', 'is_enabled', 'order_position'
        );
    }

    public static function tableName()
    {
        return 'library_category';
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