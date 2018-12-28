<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 11.05.2016
 */

namespace components\Component\Loto\items;


use components\Component\Loto\validators\iValidator;

class Custom extends BaseItem
{
    /** @var iValidator */
    protected $validator;

    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /** @var iItem */
    protected $give_item = null;
    public function give($owner)
    {
        $this->give_item = $this->validator->loto($owner);
        $this->give_item->setMessage($this->message);

        return $this->give_item->give($owner);
    }

    public function getViewItems()
    {
        $response = array();
        foreach ($this->validator->view() as $item) {
            $response[] = $item->getItem();
        }

        return $response;
    }

    public function getItem()
    {
        return $this->give_item->getItem();
    }

    public function getItemLoto()
    {
        return $this->give_item->getItemLoto();
    }
}