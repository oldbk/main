<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Quests\pocket\itemInfo;

use components\Component\Quests\pocket\iPocketItemInfo;

abstract class BaseInfo implements iPocketItemInfo, iRewardItem
{
    const ITEM_TYPE_ITEM            = 'item';
    const ITEM_TYPE_CUSTOM_ITEM     = 'custom_item';
    const ITEM_TYPE_ABILITY_OWN     = 'ability';
    const ITEM_TYPE_EXP             = 'exp';
    const ITEM_TYPE_REPA            = 'repa';
    const ITEM_TYPE_KR              = 'kr';
    const ITEM_TYPE_EKR             = 'ekr';
    const ITEM_TYPE_MEDAL           = 'medal';
    const ITEM_TYPE_WEIGHT          = 'weight';
    const ITEM_TYPE_PROF_EXP        = 'prof_exp';

    public $name;

    /**
     * @param $type
     * @return iPocketItemInfo
     */
    public static function getItemInfo($type)
    {
        $type = str_replace(' ', '', ucwords(str_replace('_', ' ', $type)));
        $className = sprintf('components\Component\Quests\pocket\itemInfo\\%sInfo', ucfirst($type));
        try {
            return new $className();
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function populate(array $attributes)
    {
        foreach ($attributes as $field => $value) {
            if(property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}