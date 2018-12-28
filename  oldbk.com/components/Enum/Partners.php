<?php

namespace components\Enum;


class Partners
{
    public static function getContent($pid)
    {

        try {
            $file = ROOT_DIR . '/template/views/stubs/partners/' . $pid . '.php';

            if (file_exists($file)) {
                return $file;
            }

            return false;


        } catch (\Exception $e) {

        }

    }
}