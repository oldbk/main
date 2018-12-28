<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.10.2015
 */

$mode = 'production';
if($_SERVER['SERVER_NAME'] == 'oldbk.local' || PHP_OS === 'Darwin') {
    $mode = 'local';
} elseif($_SERVER['SERVER_NAME'] == 'dev.oldbk.com') {
    $mode = 'development';
}

require_once __DIR__ . '/bootstrap_base.php';


$key = $app->session->get('test_key');// для чего это???
/*if (PRODUCTION_MODE) {
    //записываем все данные запроса в ежедневный файл лога. логи старше 30 дней сами удаляются
    \components\Component\Slim\Log\MonologWriter::manualWriteData(
        'alldata',
        'All data',
        [
            'day' => 30,
            'level' => 'debug'
        ],
        true,
        false,
        'log'
    );
}*/
