<?php

namespace components\Helper;


use components\Component\Slim\Slim;
use components\Enum\DenyText;
use components\Exceptions\ForumException;

/**
 * Class Rvs
 * @package components\Helper
 */
class Rvs
{

    public static function detect($params, $rules, $xhr = false, $txt = false)
    {

        $return = false;

        foreach ($rules as $field => $rule) {
            foreach ($rule as $r) {
                switch ($r) {
                    case 'containsRegex':
                        {
//                            if (Str::containsRegex(substr($params[$field], -4), DenyText::$regexes)) {
                            if (Str::containsRegex($params[$field], DenyText::$regexes)) {
                                static::block();
                                static::rvsLog(2,'rvs_log');
                                $return = true;
                            }
                            break;
                        }

                    case 'contains':
                        {
                            if (Str::contains($params[$field], ($txt !== false ? $txt + DenyText::$texts : DenyText::$texts))) {
                                static::block();
                                static::rvsLog(3,'rvs_log');
                                $return = true;
                            }
                            break;
                        }

                    case 'rvs_links':
                        {
                            $s = static::rvsLinks($params[$field]);

                            if ($s[0] === 'DEL') {
//                                static::block();
                                static::rvsLog(5,'rvs_log', $s[1]);
                                $return = $field;
                            }
                            break;
                        }
                }
            }
        }

        if ($xhr) {
            $app = Slim::getInstance();

            if ($app->request->isXhr()) {
//                static::block();
                static::rvsLog(4,'rvs_log');
                $return = true;
            }
        }

        return $return;
    }

    protected static function block()
    {
        if ($user = \Auth::user()) {
            $user->update([
                'block' => 1
            ]);
        }
    }

    protected static function rvsLog($type, $file = 'rvs_log', $add = '')
    {
        \components\Helper\FileHelper::write2(
            "type$type:" . serialize(array_merge($_GET, $_POST, $_SERVER, (\Auth::user() ? \Auth::user()->toArray() : []), $_SESSION)) . $add . "\r\n",
            $file
        );
    }

    private static function rvsLinks($where_to_search)
    {
        $to_lichka = $where_to_search;
        $where_to_search = mb_strtolower($where_to_search);
        setlocale(LC_ALL, 'ru_RU');

        $iskluchenija = DenyText::$exceptions;

        $where_to_search = preg_replace('/(.)\\1{3,}/i', '$1', $where_to_search);
        $where_to_search = preg_replace('/\s{2,}/', '$1', $where_to_search);

        for ($d = 0; $d < count($iskluchenija); $d++) {
            $where_to_search = preg_replace('/' . $iskluchenija[$d] . '/i', '', $where_to_search);
        }


        $kill = 0;
        $return = 'OK';

        $search = DenyText::$rvs_links;

        $map_en = Array(
            '-' => '–—', 'a' => 'а', 'b' => 'бв', 'c' => 'сцк', 'd' => 'д', 'e' => 'еф', 'f' => 'фй', 'j' => 'г',
            'h' => 'х', 'i' => 'йи1іа', 'g' => 'г', 'k' => 'к', 'l' => '1л', 'm' => 'м', 'n' => 'н', 'o' => 'о0',
            'p' => 'п', 'q' => 'ку', 'r' => 'р', 's' => 'с', 't' => 'т', 'u' => 'у', 'v' => 'в', 'w' => 'в', 'x' => 'х', 'y' => 'ю', 'y' => 'у', 'z' => 'з');

        setlocale(LC_ALL, 'ru_RU');

        for ($f = 0; $f < count($search); $f++) {
            $regexp = '';
            $replacement = '<font color=red><b>';
            for ($i = 0; $i <= strlen($search[$f]) - 1; $i++) {
                //побуквенно собираем маски поиска.
                //'Берем адрес из словаря fdworlds.net и кладем его на маску, чтоб определять
                // f*dw*or*ld*s.net  f-d-w-o-r-lds.net  f d w o r l d s(точка)net  и тд
                //'[fф](\W*)[dд](\W*)[wв](\W*)[oо](\W*)[rр](\W*)[lл](\W*)[dд](\W*)[sс](\W*)(.?){10}(\W*)[nн](\W*)[eеэ](\W*)[tт]'
                if ($search[$f][$i] == '.') {
                    $regexp = substr($regexp, 0, -5);
                    $regexp .= '(.?){10}(\W*)';
                } else {
                    $regexp .= '[' . $search[$f][$i] . ($map_en[$search[$f][$i]] ?? '') . ']';
                    if ($i < strlen($search[$f]) - 1) {
                        $regexp .= '(\W*)';
                    }
                    $replacement .= "\$" . $i;
                }
            }
            $replacement .= '</b></font>';
            $regexp = '/' . $regexp . '/i';
            if (preg_match($regexp, $where_to_search)) {
                $to_lichka = preg_replace($regexp, $replacement, $to_lichka) . '. Отработал фильтр на сайт:' . $search[$f];
                $kill = 1;
                break;
            }
        }

        if ($kill == 1) {

            if ($user = \Auth::user()) {
                if (($user['align'] > 1 && $user['align'] < 2) || ($user['align'] > 2 && $user['align'] < 3)) {
                    return "OK";
                }
            }
            $return = 'DEL';
        }

        return [
            $return,
            $to_lichka,
        ];
    }

}