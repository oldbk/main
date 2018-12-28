<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.06.2016
 */

namespace components\Component\Quests\object;


abstract class Base
{
    public function __construct(array $attributes = array())
    {
        foreach ($attributes as $field => $value) {
            if(property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }
}