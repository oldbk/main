<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\check\CheckerMagic;
use components\Component\VarDumper;

class MagicTask extends BaseTask
{
    public $magic_id;

    public function getItemType()
    {
        return self::ITEM_TYPE_MAGIC;
    }

    /**
     * @param CheckerMagic $Checker
     * @return bool
     */
    public function check($Checker)
    {
        $magic_ids = explode(',', $this->magic_id);
        return in_array($Checker->magic_id, $magic_ids);
    }
}