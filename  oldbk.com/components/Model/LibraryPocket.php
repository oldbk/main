<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;

use components\Helper\ShopHelper;
use components\Model\LibraryItem;

use components\Model\Shop;
use components\Object\Item;
use database\DB;

class LibraryPocket extends AbstractCapitalModel
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
            'id', 'name', 'created_at'
        );
    }

    public static function tableName()
    {
        return 'library_pocket';
    }

    public static function pkField()
    {
        return 'id';
    }

    public function getPk()
    {
        return $this->id;
    }
    public function getPocketItems($pocket_id) {
	$query = '
		SELECT s.* FROM '.LibraryItem::tableName().' p LEFT JOIN '.ShopHelper::getFromType(ShopHelper::TYPE_SHOP).' s ON s.id = p.item_id WHERE pocket_id = '.$pocket_id.' and shop_id = '.ShopHelper::TYPE_SHOP.' AND s.id is not null
		UNION
		SELECT s.* FROM '.LibraryItem::tableName().' p LEFT JOIN '.ShopHelper::getFromType(ShopHelper::TYPE_ESHOP).' s ON s.id = p.item_id WHERE pocket_id = '.$pocket_id.' and shop_id = '.ShopHelper::TYPE_ESHOP.'  AND s.id is not null
		UNION
		SELECT s.* FROM '.LibraryItem::tableName().' p LEFT JOIN '.ShopHelper::getFromType(ShopHelper::TYPE_CSHOP).' s ON s.id = p.item_id WHERE pocket_id = '.$pocket_id.' and shop_id = '.ShopHelper::TYPE_CSHOP.'  AND s.id is not null
	';
	$ret = array();
	$t = $this->db()->executeQuery($query);
	while($res = $t->fetch(DB::FETCH_ASSOC)) {
		$ret[] = $res;
	}
	return $ret;
    }
}