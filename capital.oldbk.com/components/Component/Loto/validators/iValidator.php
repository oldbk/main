<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 11.05.2016
 */

namespace components\Component\Loto\validators;

use components\Component\Loto\items\iItem;

interface iValidator
{
    /**
     * @return iItem[]
     */
    public function view();

    /**
     * @param array $owner
     * @return iItem
     */
    public function loto(array $owner);

    /**
     * @param $app
     * @return self
     */
    public function setApp($app);

    /**
     * @param $loto_id
     * @return self
     */
    public function setLotoId($loto_id);

    /**
     * @param $item
     * @return self
     */
    public function setItem($item);

    /**
     * @param $message
     * @return self
     */
    public function setMessage($message);
}