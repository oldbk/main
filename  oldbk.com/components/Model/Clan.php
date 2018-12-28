<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;

use database\DB;
use database\Query;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $short
 * @property string $name
 * @property string $descr
 * @property int $glava
 * @property string $vozm
 * @property string $align
 * @property string $mshadow
 * @property string $wshadow
 * @property string $homepage
 * @property string $chat
 * @property string $rekrut1
 * @property string $rekrut2
 * @property int $rekrut_klan
 * @property int $base_klan
 * @property int $voinst
 * @property int $messages
 * @property int $defch
 * @property int $tax_date
 * @property int $tax_timer
 * @property int $msg
 * @property int $time_to_del
 * @property int $warcancel
 *
 */
class Clan extends AbstractCapitalModel
{
    /**
     * @param string $className
     * @return Clan
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function tableName()
    {
        return 'clans';
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
        return array('id', 'short', 'name', 'descr', 'glava', 'vozm', 'align', 'mshadow', 'wshadow', 'homepage',
            'chat', 'rekrut1', 'rekrut2', 'rekrut_klan', 'base_klan', 'voinst', 'messages', 'defch', 'tax_date',
            'tax_timer', 'msg', 'time_to_del', 'warcancel');
    }

    public function getClansList()
    {
        $query = new Query();
        $query->select("c1.*, c2.*")
            ->from("clans c1")
            ->join("LEFT JOIN clans c2 ON c1.rekrut_klan = c2.id")
            ->where("c1.base_klan = 0 and c1.time_to_del=0 and c1.short not in ('3testers','3testers1','4testers','4testers1','5testers','6testers','6testers1','ytesters','ztesters','xtesters','tt', 'ràdminion')")
            ->orderBy("c1.short");
        $this->db()->setAttribute(DB::ATTR_FETCH_TABLE_NAMES, true);
        $t = $this->db()->executeQuery($query->getQuery());

        $clans = [];

        while ($c = $t->fetch(DB::FETCH_ASSOC)) {
            $clans[] = $c;
        }
        return $clans;
    }

    public function getClanSite($short)
    {
        try {

            $query = new Query();
            $query->select("*")
                ->from("topsites.top")
                ->where("klan = ?");


            $t = $this->db()->executeQuery($query->getQuery(), [$short]);
            if ($t) {
                return $t->fetch(DB::FETCH_ASSOC);
            }
            return false;

        } catch (\PDOException $PDOException) {
            return false;
        }
    }

}