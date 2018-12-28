<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 14.04.2016
 */

namespace components\Component\Loto\items;


use components\Component\Slim\Slim;
use components\Component\VarDumper;
use components\models\Chat;

abstract class BaseItem implements iItem
{
    /** @var Slim */
    protected $app;
    protected $loto_id;
    protected $loto_item;
    /** @var iItem[] */
    protected $other_items = array();
    protected $_debug;

    protected $item;
    protected $count;

    public function __construct($app, $loto_id, $loto_item)
    {
        $this->app = $app;
        $this->loto_id = $loto_id;
        $this->item = $this->prepareItem($loto_item);

        $this->loto_item = $loto_item;
        $this->count = $loto_item['count'] - $loto_item['use_count'];
    }

    protected $message = null;
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function addOtherItem($item)
    {
        $this->other_items[] = $item;
        return $this;
    }

    protected function prepareItem($loto_item)
    {
        return array('getfrom' => 2);
    }

    protected function debug($message)
    {
        if($this->_debug) {
            VarDumper::d($message, false);
        }
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getItemLoto()
    {
        return $this->loto_item;
    }

    /**
     * @param $owner
     * @return bool
     */
    protected function sendMessage($owner)
    {
        if(!$this->message) {
            return false;
        }

        $message = str_replace(array('%loto_num%', '%gift%'), array($this->loto_id, $this->item['name']), $this->message);
        if(Chat::addToChatSystem($message, $owner) == false) {
            return false;
        }

        return true;
    }

    public function getCount()
    {
        return $this->count;
    }
    
    public function getViewItems()
    {
        $response = array($this->getItem());
        foreach ($this->other_items as $item) {
            $response[] = $item->getItem();
        }

        return $response;
    }

    public function getCategory()
    {
        return $this->loto_item['category_id'];
    }
}