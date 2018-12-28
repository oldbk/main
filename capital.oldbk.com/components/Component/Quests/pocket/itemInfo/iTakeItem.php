<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Component\Quests\pocket\itemInfo;


use components\Component\Quests\object\Part;
use components\Component\Quests\object\Take;
use components\models\User;

interface iTakeItem
{
    /**
     * @param User $owner
     * @param Part $Part
     * @param Take $Take
     * @return bool
     * @throws \Exception
     */
    public function take($owner, Part $Part, Take $Take);
}