<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 03.04.2016
 */

namespace components\Component\Loto;


use components\Component\AbstractComponent;
use components\Component\Loto\items\Ability;
use components\Component\Loto\items\Custom;
use components\Component\Loto\items\iItem;
use components\Component\Loto\items\Item;
use components\Component\Loto\validators\ElementUsilValidator;
use components\Component\Loto\validators\iValidator;
use components\Component\VarDumper;
use components\Helper\ShopHelper;
use components\models\ItemLotoRas;
use components\models\LotoItem;
use components\models\LotoItemInfo;
use components\models\Magic;

abstract class BaseLoto extends AbstractComponent
{
    /** @var iItem[] */
    protected $item_list = array();
    protected $item_view = array();
    protected $item_count_by_id = array();
    protected $item_use_count = array();
    protected $item_stock_ids = array();

    protected $loto_id;
    protected $loto;

    protected $_debug = false;

    public $message_private = 'Розыгрыш лотереи №%loto_num% завершен! Ваш приз: %gift%';
    public $message_finish = '[Комментатор] Внимание! Состоялся розыгрыш %loto_num% тиража Лотереи ОлдБК. Топ-100 победителей можно посмотреть в здании ЛОТО на ЦП.';
    public $message_other = array();

    public function __construct($loto_num = null)
    {
        $this->loto_id = $loto_num;
        $this->prepareLoto();

        parent::__construct();
    }

    public function getItemCountById()
    {
        return $this->item_count_by_id;
    }
    
    public function getViewList()
    {
        return $this->item_view;
    }

    public function setDebug()
    {
        $this->_debug = true;
    }

    public function getLotoId()
    {
        return $this->loto_id;
    }

    public function getLoto()
    {
        return $this->loto;
    }

    protected $_validators = array();
    protected function getValidator($name)
    {
        if(isset($this->_validators[$name])) {
            return $this->_validators[$name];
        }

        switch ($name) {
            case 'element_usil':
                $this->_validators[$name] = new ElementUsilValidator();
                break;
        }

        return $this->_validators[$name];
    }

    /**
     *
     */
    protected function buildItemList()
    {
        $item_list = $this->getItemList();
        $this->getPrototypes($item_list);

        foreach ($item_list as $loto_item_id => $item) {
            if(($_item = $this->callPrepare($item)) !== null) {
                $this->item_list[$loto_item_id] = $_item;
            }
        }
    }

    protected function callPrepare($item)
    {
        $name = str_replace('_', ' ', $item['item_name']);
        $name = str_replace(' ', '', ucwords($name));

        $method = sprintf('prepare%s', $name);
        if(method_exists($this, $method)) {
            return $this->{$method}($item);
        }

        return null;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function prepareLoto()
    {
        if($this->loto_id === null) {
            $this->loto = ItemLotoRas::where('status', '=', 1)->first();
        } else {
            $this->loto = ItemLotoRas::find($this->loto_id);
        }
        if (!$this->loto) {
            throw new \Exception();
        }
		$this->loto = $this->loto->toArray();

        $this->loto_id = $this->loto['id'];
        return true;
    }

    /**
     * @param $item
     * @return Item
     */
    protected function prepareItem($item)
    {
        $shop_id = $item['info']['shop_id'];
        $item_id = $item['info']['item_id'];
        $prototype = $this->item_prototypes[$shop_id][$item_id];

        $_Item = new Item($this->app(), $this->loto_id, $item, $prototype);
        $_Item->setMessage($this->message_private);
        return $_Item;
    }

    /**
     * @param $item
     * @return Custom
     */
    protected function prepareCustomItem($item)
    {
        $_arr = explode('.', $item['info']['get_method']);
        $get_method = $_arr[1];

        /** @var iValidator $validator */
        $validator = $this->getValidator($get_method);
        $validator->setApp($this->app())
            ->setLotoId($this->loto_id)
            ->setItem($item);

        $_Item = new Custom($this->app(), $this->loto_id, $item);
        $_Item->setValidator($validator);
        $_Item->setMessage($this->message_private);
        return $_Item;
    }

    /**
     * @param $item
     * @return Ability
     */
    protected function prepareAbility($item)
    {
        $prototype = $this->ability_prototypes[$item['info']['magic_id']];
        $_Item = new Ability($this->app(), $this->loto_id, $item, $prototype);
        $_Item->setMessage($this->message_private);

        return $_Item;
    }

    /**
     * @return array
     */
    protected function getItemList()
    {
		$LotoItem = LotoItem::where('loto_num', '=', $this->loto_id)
			->orderBy('cost_ekr', 'desc')
			->orderBy('cost_kr', 'desc')
			->get()->toArray();

        $item_list = array();
        foreach ($LotoItem as $Item) {
            $item_list[$Item['id']] = $Item;

            $this->item_count_by_id[$Item['id']] = $Item['count'] - $Item['use_count'];
            $this->item_use_count[$Item['id']] = 0;
            if ($Item['stock'] > 0) {
                $this->item_stock_ids[] = $Item['id'];
            }
        }

        return $this->getInfo($item_list);
    }

    /**
     * @param $item_list
     * @return array
     */
    protected function getInfo($item_list)
    {
        $LotoItemInfo = LotoItemInfo::where('loto_num', '=', $this->loto_id)->get()->toArray();
        foreach ($LotoItemInfo as $_item) {
            if(!isset($item_list[$_item['item_id']])) {
                continue;
            }

            $item_list[$_item['item_id']]['info'][$_item['field']] = $_item['value'];
        }

        return $item_list;
    }

    protected $item_prototypes = array();
    protected $ability_prototypes = array();

    /**
     * @param $item_list
     * @return array
     */
    protected function getPrototypes($item_list)
    {
        $prototype_item_ids = array(
            ShopHelper::TYPE_SHOP   => array(),
            ShopHelper::TYPE_ESHOP  => array(),
            ShopHelper::TYPE_CSHOP  => array(),
        );

        $prototypes_ability_ids = array();
        foreach ($item_list as $loto_item_id => $_item) {
            switch ($_item['item_name']) {
                case 'item':
                    $shop_id = $_item['info']['shop_id'];
                    $item_id = $_item['info']['item_id'];

                    $prototype_item_ids[$shop_id][] = $item_id;
                    break;
                case 'ability':
                    $prototypes_ability_ids[] = $_item['info']['magic_id'];
                    break;
            }
        }

        foreach ($prototype_item_ids as $shop_id => $item_ids) {
            if(empty($item_ids)) {
                continue;
            }

            foreach (ShopHelper::getPrototypes($item_ids, $shop_id) as $_item_prototype) {
                $this->item_prototypes[$shop_id][$_item_prototype['id']] = $_item_prototype;
            }
        }

        if($prototypes_ability_ids) {
            $MagicList = Magic::whereIn('id', $prototypes_ability_ids)->get()->toArray();
            foreach ($MagicList as $Magic) {
                $this->ability_prototypes[$Magic['id']] = $Magic;
            }
        }
    }

    /**
     * @return int
     */
    protected function getOriginalLotoItemId()
    {
        $keys = array_keys($this->item_count_by_id);
        $random_start_key = rand(0, count($keys) - 1);

        $this->debug(sprintf('Ищем награду. Стартовое значение для поиска по списку %d (ID из админки)', $keys[$random_start_key]));
        if(($loto_item_id = $this->getLotoItemKey($random_start_key)) === false) {
            $this->debug('Не нашли базовый предмет для награды');
            return false;
        }

        $this->debug(sprintf('Нашли базовый предмет для награды. ID в админке: %d', $loto_item_id));

        return $loto_item_id;
    }

    protected function getLotoItemKey($random_start_key)
    {
        $keys = array_keys($this->item_count_by_id);
        foreach ($keys as $key => $loto_item_id) {
            if($key < $random_start_key) {
                continue;
            }

            if($this->item_count_by_id[$loto_item_id] - $this->item_use_count[$loto_item_id] > 0) {
                return $loto_item_id;
            }

            if($key == count($keys) - 1 && $random_start_key != 0) {
                $this->debug('Подошли к концу списка, выставляем в начало');
                return $this->getLotoItemKey(0);
            }
        }

        $this->debug('Базовые предметы закончились');
        return false;
    }

    protected function getStockLotoItemId()
    {
        $this->debug('Берем из стока');
        $loto_item_id = $this->item_stock_ids[rand(0, count($this->item_stock_ids) - 1)];

        $this->debug(sprintf('Нашли из стока предмет для награды. ID в админке: %d', $loto_item_id));
        return $loto_item_id;
    }

    /**
     * @return int
     */
    protected function getItemLotoId()
    {
        if(($loto_item_id = $this->getOriginalLotoItemId()) === false) {
            $loto_item_id = $this->getStockLotoItemId();
        }

        return $loto_item_id;
    }

    public function &getItem()
    {
        $loto_item_id = $this->getItemLotoId();

        $item =& $this->item_list[$loto_item_id];

        return $item;
    }

    public function getItemByName($name)
    {
        foreach ($this->item_list as $_item) {
            $item = $_item->getItem();
            if($item['name'] == $name) {
                return $_item;
            }
        }

        return null;
    }

    protected function debug($message)
    {
        if($this->_debug) {
            VarDumper::d($message, false);
        }
    }
}