<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;

class LibraryPage extends AbstractCapitalModel
{
    /**
     * @param string $className
     * @return Magic
     */

    const TYPE_NONE         = 0;
    const TYPE_ACTION       = 1;
    const TYPE_EVENT        = 2;
    const TYPE_QUEST        = 3;


    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function fieldMap()
    {
        return array(
            'id', 'dir', 'category_id', 'page_title', 'page_description', 'body', 'var_from','var_to','type','is_enabled', 'order_position',
            'updated_at', 'created_at'
        );
    }

    public static function tableName()
    {
        return 'library_page';
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