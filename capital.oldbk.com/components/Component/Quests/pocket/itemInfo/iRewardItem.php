<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Component\Quests\pocket\itemInfo;


use components\Component\Quests\object\Part;
use components\Component\Quests\object\Reward;
use components\models\User;

interface iRewardItem
{
    /**
     * @param User $owner
     * @param Part $Part
     * @param Reward $Reward
     * @return bool
     * @throws \Exception
     */
    public function give($owner, Part $Part, Reward $Reward);

    public function getName();
}