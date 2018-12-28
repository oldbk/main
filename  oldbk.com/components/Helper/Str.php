<?php

namespace components\Helper;


class Str extends \Illuminate\Support\Str
{

    /**
     * @param $html
     * @return string
     * @deprecated
     */
    public static function _closetags($html)
    {
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];

        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i = 0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</' . $openedtags[$i] . '>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }

    /**
     * @param $html
     * @return string
     */
    public static function closetags($html)
    {
        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);

        $openedtags = $result[1];

        preg_match_all('#</([a-z]+)>#iU', $html, $result);

        $closedtags = $result[1];

        $len_opened = count($openedtags);

        if (count($closedtags) == $len_opened) {

            return $html;

        }

        $openedtags = array_reverse($openedtags);

        for ($i = 0; $i < $len_opened; $i++) {

            if (!in_array($openedtags[$i], $closedtags)) {

                $html .= '</' . $openedtags[$i] . '>';

            } else {

                unset($closedtags[array_search($openedtags[$i], $closedtags)]);

            }

        }

        return $html;
    }

    /**
     * @param string $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    public static function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strwidth($value, 'WINDOWS-1251') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'WINDOWS-1251')) . $end;
    }

    /**
     * @param $haystack
     * @param $needles
     * @return bool
     */
    public static function containsRegex($haystack, $needles)
    {
        foreach ((array)$needles as $regex) {
            if (preg_match('~' . $regex . '~iU', $haystack)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $text
     * @return mixed
     */
    public static function makeLink($text)
    {
        return preg_replace('#\b(https?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/)))#', '<a href="$1" target="_blank">$1</a>', $text);
    }

    /**
     * @param $text
     * @return mixed
     */
    public static function stripTagAttributes($text)
    {
        return preg_replace('/<(\w+)[^>]*>/i', '<\1>', $text);
    }

    /**
     * @param $input
     * @return mixed
     */
    public static function br2nl($input)
    {
        return preg_replace('/<br(\s+)?\/?>/i', "\n", $input);
    }

}