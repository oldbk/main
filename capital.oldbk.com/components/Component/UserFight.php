<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 03.01.2016
 */

namespace components\Component;


class UserFight
{
    public $type    = 0;
    public $damage  = 0;
    public $is_win  = false;
    public $is_loss = false;
    public $bot_id  = 0;
    public $cp_btn_fight = false;
    public $comment = null;
    public $teams = null;
    public $battle_data = array();

    public function __construct($property = array())
    {
        foreach ($property as $field => $value) {
            if(property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }
}