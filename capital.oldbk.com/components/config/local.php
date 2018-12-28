<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 09.11.2015
 */
return array(
    'log.enable'            => true,
    'log.level'             => \Slim\Log::DEBUG,
    'debug'                 => true,
    //link
    'url.capital'           => 'http://capitalcity.oldbk.local',
    'url.oldbk'             => 'http://oldbk.local',
    'url.chat'              => 'http://chat.oldbk.local',
    //db
    'db.capital'                    => array(
        'host'              => 'localhost',
        'dbname'            => 'oldbk',
        'username'          => 'oldbk',
        'password'          => 'oldbk',
        'charset'           => 'cp1251'
    ),
    //sn
    'sn.facebook'           => '\components\Controller\oauth\FacebookController',
    'sn.google'             => '\components\Controller\oauth\GoogleController',
    'sn.instagram'          => '\components\Controller\oauth\InstagramController',
    'sn.mailru'             => '\components\Controller\oauth\MailruController',
    'sn.ok'                 => '\components\Controller\oauth\OkController',
    'sn.twitter'            => '\components\Controller\oauth\TwitterController',
    'sn.vkontakte'          => '\components\Controller\oauth\VkontakteController',
);