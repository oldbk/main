<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.06.2016
 */

namespace components\Component\Quests\pocket;


interface iPocketItemInfo
{
    public function getItemType();
    public function populate(array $attributes);
}