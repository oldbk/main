<?php

namespace components\Helper;

use components\Eloquent\Chat;

/**
 * Class ToChat
 * @package components\Helper
 */
class ToChat
{

    /**
     * @param $text
     * @param null $user
     * @return mixed
     */
    public static function sendSys($text, $user = null)
    {
        $room = $user ? $user['room'] : 0;
        $txt_to_file = ":[" . time() . "]:[!sys!!]:[" . ($text) . "]:[" . $room . "]";
        $room = -1; // TEST only by Fred

        return static::send([
            'text' => $txt_to_file,
            'room' => $room,
        ]);

    }

    /**
     * @param $text
     * @param $ids
     * @return mixed
     */
    public static function sendGroup($text, $ids)
    {
        if (is_array($ids)) {
            $ids = implode(":|:", $ids);
        }

        $txt_to_file = ":[" . time() . "]:[!group!:|:" . $ids . "]:[" . ($text) . "]:[]";

        return static::send([
            'text' => $txt_to_file,
        ]);
    }

    /**
     * @param $text
     * @param $who
     * @param $user
     * @return mixed
     */
    public static function sendPrivate($text, $who, $user)
    {
        $room = $user ? $user['room'] : 0;

        $txt_to_file = ":[" . time() . "]:[{$who}]:[" . ($text) . "]:[" . $room . "]";
        $room = -1; // TEST only by Fred

        return static::send([
            'text' => $txt_to_file,
            'room' => $room,
        ]);
    }

    /**
     * @param $params
     * @return mixed
     */
    public static function send($params)
    {
        return Chat::create($params);
    }
}