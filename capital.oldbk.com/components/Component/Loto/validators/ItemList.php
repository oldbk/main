<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 14.04.2016
 */

namespace components\Component\Loto\validators;


class ItemList
{
    private $use_item;
    private $all_item;

    public function getAll()
    {
        return array_merge($this->all_item, array($this->use_item));
    }

    public function setUseItem($item)
    {
        $this->use_item = $item;
        return $this;
    }

    public function getUseItem()
    {
        return $this->use_item;
    }

    public function getAllItem()
    {
        return $this->all_item;
    }

    public function addToAll($items)
    {
        $this->all_item[] = $items;
        return $this;
    }
}