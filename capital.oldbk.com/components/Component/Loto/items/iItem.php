<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 14.04.2016
 */

namespace components\Component\Loto\items;


interface iItem
{
    /**
     * @param $owner
     * @return boolean
     */
    public function give($owner);

    /**
     * @return int
     */
    public function getCount();

    /**
     * @return array
     */
    public function getViewItems();

    /**
     * @return int
     */
    public function getCategory();

    /**
     * @return array
     */
    public function getItem();

    /**
     * @return mixed
     */
    public function getItemLoto();

    /**
     * @param $item
     * @return self
     */
    public function addOtherItem($item);

    /**
     * @param $message
     * @return self
     */
    public function setMessage($message);
}