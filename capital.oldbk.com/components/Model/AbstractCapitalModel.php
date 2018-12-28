<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 21.11.2015
 */

namespace components\Model;

/**
 * Class AbstractCapitalModel
 * @package components\Model
 */
abstract class AbstractCapitalModel extends AbstractModel
{
    /**
     * @param string $className
     * @return $this
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function connectionName()
    {
        return 'capital';
    }
}