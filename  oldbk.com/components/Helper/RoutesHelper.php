<?php


namespace components\Helper;

/**
 * Class RoutesHelper
 * @package components\Helper
 */
class RoutesHelper
{

    /**
     * @return mixed
     * В папке не должно быть ничего кроме файлов роутинга!!!
     */
    public static function getRoutes()
    {
        return \Storage::files('/components/Routes');
    }
}