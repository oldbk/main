<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 21.11.2015
 */

namespace components\Model;


interface iModel
{
    public static function tableName();
    public static function pkField();
    public static function connectionName();
    public function getPk();
}