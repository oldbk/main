<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;

class LibraryItem extends AbstractCapitalModel
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
            'id', 'pocket_id', 'item_id', 'shop_id'
        );
    }

    public static function tableName()
    {
        return 'library_item';
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