<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Slim
    |--------------------------------------------------------------------------
    */
    'log.enabled'           => true,
    'log.level'             => \Slim\Log::DEBUG,
    'debug'                 => true,

    'url.capital'           => 'http://capitalcity.oldbk.local',
    'url.oldbk'             => 'http://oldbk.local',
    'url.chat'              => 'http://chat.oldbk.local',
    'url.jsdomain'          => 'oldbk.local',

    'path'                  => ROOT_DIR,
    'path.lang'             => ROOT_DIR . '/components/lang/',
    'path.cache'            => ROOT_DIR . '/cache/',

    'gzip'                  => false,

    'cookies.domain'        => 'oldbk.local',

    /*
    |--------------------------------------------------------------------------
    | News
    |--------------------------------------------------------------------------
    */
    'news' => [
        'comments' => false
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation
    |--------------------------------------------------------------------------
    */
    'app.locale' => 'ru',
    'app.fallback_locale' => 'ru',


    /*
    |--------------------------------------------------------------------------
    | File Systems
    |--------------------------------------------------------------------------
    */

    'filesystems' => [
        'default' => 'local',
        'cloud' => 's3',
        'disks' => [

            'local' => [
                'driver' => 'local',
                'root' => ROOT_DIR,
            ],

            's3' => [
                'driver' => 's3',
                'key' => '',
                'secret' => '',
                'region' => '',
                'bucket' => '',
            ],

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */
    'database.fetch' => PDO::FETCH_CLASS,
    'database.default' => 'capital',
    'database.connections' => array(
        'capital' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'oldbk',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'cp1251',
            'collation' => 'cp1251_general_ci',
            'prefix'    => '',
            'timezone'  => '+03:00',
            'strict'    => false,
//            'engine'    => null,
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | ElasticSearch
    |--------------------------------------------------------------------------
    */
    'elastica.config' => [
        'host'     => 'localhost',
        'port'     => 9200,
    ],

    'elastica.default_index' => 'oldbk',

    /*
    |--------------------------------------------------------------------------
    | Social Network
    |--------------------------------------------------------------------------
    */
    'sn.facebook'           => '\components\Controller\oauth\FacebookController',
    'sn.google'             => '\components\Controller\oauth\GoogleController',
    'sn.instagram'          => '\components\Controller\oauth\InstagramController',
    'sn.mailru'             => '\components\Controller\oauth\MailruController',
    'sn.ok'                 => '\components\Controller\oauth\OkController',
    'sn.twitter'            => '\components\Controller\oauth\TwitterController',
    'sn.vkontakte'          => '\components\Controller\oauth\VkontakteController',
    'aws'					=> [
        'key' 		=> 'AKIAI7UQABVHUXWNAG2Q',
        'secret' 	=> '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'
    ],


    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    */
    'providers'             => [
        \components\Providers\ConfigServiceProvider::class,

        \Illuminate\Events\EventServiceProvider::class,
        \components\Providers\DatabaseServiceProvider::class,
        \components\Providers\CacheServiceProvider::class,
        \components\Providers\ConfigKoServiceProvider::class,
        \Illuminate\Filesystem\FilesystemServiceProvider::class,
        \components\Providers\TranslationServiceProvider::class,
        \Illuminate\Validation\ValidationServiceProvider::class,
        \components\Providers\XssServiceProvider::class,
        \components\Providers\AuthServiceProvider::class,

        //always last
        \components\Providers\AppServiceProvider::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | Aliases
    |--------------------------------------------------------------------------
    */
    'aliases' => [
        'App'       => \components\Facade\App::class,
        'Route'     => \components\Facade\Route::class,
        'DB'        => \Illuminate\Support\Facades\DB::class,
        'Validator' => \Illuminate\Support\Facades\Validator::class,
        'Cache'     => \Illuminate\Support\Facades\Cache::class,
        'Event'     => \Illuminate\Support\Facades\Event::class,
        'Lang'      => \Illuminate\Support\Facades\Lang::class,
        'Xss'       => \components\Facade\Xss::class,
        'Config'    => \components\Facade\Config::class,
        'Auth'      => \components\Facade\Auth::class,
        'File'      => \Illuminate\Support\Facades\File::class,
        'Storage'   => \Illuminate\Support\Facades\Storage::class,
        'ConfigKo'  => \components\Facade\ConfigKo::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | DebugBar
    |--------------------------------------------------------------------------
    */
    'debugbar' => [
        'debug' => true,
    ],
];