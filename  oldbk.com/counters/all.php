<?php
//Так как для украины блок яндекса и мейл ру, не грузим эти счетчики

defined("ROOT_DIR") or define('ROOT_DIR', realpath(__DIR__ . '/../'));

include(ROOT_DIR . "/GeoIP/geoip.inc");
include(ROOT_DIR . "/GeoIP/geoipregionvars.php");
$gi = geoip_open(ROOT_DIR . "/GeoIP/GeoIP.dat", GEOIP_STANDARD);

if (isset($app)) {
    $country = geoip_country_code_by_addr($gi, $app->request->getIp());
} else {
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    $country = geoip_country_code_by_addr($gi, $ip);
}


$data = "";
$data .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/counters/google.html');
$data .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/counters/li.html');


if ($country && $country !== 'UA') {
    $data .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/counters/mailru.html');
    $data .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/counters/yandex.html');
}

return $data;