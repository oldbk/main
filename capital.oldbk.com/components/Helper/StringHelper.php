<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */

namespace components\Helper;


class StringHelper
{
    public static function dayEnding($number)
    {
        if ($number < 0)
            return null;

        $ending_arr = array('день', 'дня', 'дней');

        $number = $number % 100;
        if ($number>=11 && $number<=19) {
            $ending = $ending_arr[2];
        }
        else {
            $i = $number % 10;
            switch ($i)
            {
                case (1): $ending = $ending_arr[0]; break;
                case (2):
                case (3):
                case (4): $ending = $ending_arr[1]; break;
                default: $ending = $ending_arr[2];
            }
        }

        return $ending;
    }

    public static function toArray($string)
    {
        if($string === null) {
            return array();
        }

        $string = unserialize($string);
        $data = $string === false ? array() : $string;

        return $data;
    }

    public static function toString(array $data)
    {
        return serialize($data);
    }

    public static function prepareGender($message, $gender)
    {
        $i = 0;
        while(true) {
            $i++;
            if(!preg_match('/\{gender\:(.*?)\}/i', $message, $out)) {
                break;
            }

            $sex = explode('|', $out[1]);
            if($gender == 1) {
                $message = str_replace('{gender:'.$out[1].'}', $sex[0], $message);
            } else {
                $message = str_replace('{gender:'.$out[1].'}', $sex[1], $message);
            }

            if($i == 10) {
                break;
            }
        }

        return $message;
    }
}