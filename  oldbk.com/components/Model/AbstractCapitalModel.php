<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 21.11.2015
 */

namespace components\Model;
use database\DB;

/**
 * Class AbstractCapitalModel
 * @package components\Model
 */
abstract class AbstractCapitalModel extends AbstractModel
{
    /**
     * @param string $className
     * @return AbstractCapitalModel
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function connectionName()
    {
        return 'capital';
    }

    /**
     * @return DB
     */
    public function db()
    {
        if($this->_db === null) {
            $this->_db = \components\Component\Slim\Slim::getInstance()->db;
        }

        return $this->_db;
    }
}