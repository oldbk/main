<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 29.11.2015
 */
namespace components\Helper;

class PrepareQuest
{
    public static function prepareDescription($body, $current_step)
    {
        return preg_replace(
            '/\[a(\d+)\](.+?)\[\/a\]/i',
            '<a href="javascript:void(0)" id="quest-next" data-type="$1" data-step="'.$current_step.'"><strong>$2</strong></a>',
            $body
        );
    }
}