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
 * @property int $memberid
 * @property string $sitename
 * @property string $url
 * @property string $buttonurl
 * @property string $email
 * @property string $description
 * @property string $password
 * @property int $hitsin
 * @property int $clicksin
 * @property int $hitsout
 * @property int $hitstotal
 * @property string $hitstoday
 * @property string $date
 * @property string $passreset
 * @property string $passreset2
 * @property int $rank
 * @property string $klan
 * @property int $hoststoday
 * @property int $allhosts
 * @property int $ban
 * @property int $cat
 * @property int $reg_flag
 *
 */
class Top extends AbstractTopModel
{
    /**
     * @param string $className
     * @return Top
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function tableName()
    {
        return 'top';
    }

    public static function pkField()
    {
        return 'memberid';
    }

    public function getPk()
    {
        return $this->memberid;
    }

    protected function fieldMap()
    {
        return array('memberid','sitename','url','buttonurl','email','description','password','hitsin','clicksin',
            'hitsout','hitstotal','hitstoday','date','passreset','passreset2','rank','klan','hoststoday','allhosts',
            'ban','cat','reg_flag');
    }
}