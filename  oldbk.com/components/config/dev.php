<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Slim
    |--------------------------------------------------------------------------
    */
    'log.enable'            => true,
    'log.level'             => \Slim\Log::DEBUG,
    'debug'                 => true,
    //link
    'url.capital'           => 'http://dev.capitalcity.oldbk.com',
    'url.oldbk'             => 'http://dev.oldbk.com',
    'url.chat'              => 'http://dev.chat.oldbk.com',
    'url.jsdomain'          => 'oldbk.com',


    'path'                  => ROOT_DIR,
    'path.lang'             => ROOT_DIR . '/lang/',

    'gzip'                  => true,


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
    | https://laravel.com/docs/5.4/filesystem
    |--------------------------------------------------------------------------
    */
    'filesystems.default' => 'local',
    'filesystems.cloud' => 's3',
    'filesystems.disks' => [

        'local' => [
            'driver' => 'local',
            'root' => ROOT_DIR . '/components',
        ],

        'public' => [
            'driver' => 'local',
            'root' => ROOT_DIR . '/public',
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

    ],


    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    | https://laravel.com/docs/5.4/cache
    |--------------------------------------------------------------------------
    */
    'cache.default' => 'memcached',
    'cache.stores.file' => [
        'driver' => 'file',
        'path' => ROOT_DIR . '/cache',
    ],
    'cache.stores.memcached' => [
        'driver' => 'memcached',
//        'persistent_id' => 'MEMCACHED_PERSISTENT_ID',
        'sasl' => [
//            'MEMCACHED_USERNAME',
//            'MEMCACHED_PASSWORD',
        ],
        'options' => [
//             Memcached::OPT_CONNECT_TIMEOUT  => 2000,
        ],
        'servers' => [
            [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 100,
            ],
        ],
    ],
    'cache.stores.array' => [
        'driver' => 'array',
    ],
    'cache.stores.apc' => [
        'driver' => 'apc',
    ],
    'cache.stores.database' => [
        'driver' => 'database',
        'table' => 'cache',
        'connection' => null,
    ],
    'cache.stores.redis' => [
        'driver' => 'redis',
        'connection' => 'default',//TODO if need
    ],
    'cache.prefix' => 'slim',




    /*
    |--------------------------------------------------------------------------
    | Database
    | https://laravel.com/docs/5.4/database
    |--------------------------------------------------------------------------
    */
    'database.fetch' => PDO::FETCH_CLASS,
    'database.default' => 'capital',
    'database.connections' => array(
        'capital' => array(
            'driver'    => 'mysql',
            'host'      => 'oldbkfastdb.c4c2zvyoc0zt.eu-west-1.rds.amazonaws.com',
            'database'  => 'oldbk_dev',
            'username'  => 'oldbk_dev',
            'password'  => 'n8okieO378BikFVSipuC',
            'charset'   => 'cp1251',
//            'charset'   => 'utf8',
            'collation' => 'cp1251_general_ci',
//            'collation' => 'utf8_general_ci',
            'prefix'    => '',
            'timezone'  => '+03:00',
            'strict'    => false,
//            'engine'    => null,
        ),
    ),

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
    | https://laravel.com/docs/5.4/providers
    |--------------------------------------------------------------------------
    */
    'providers'             => [
        \components\Providers\ConfigServiceProvider::class,

        \Illuminate\Events\EventServiceProvider::class,
        \components\Providers\DatabaseServiceProvider::class,


        \Illuminate\Filesystem\FilesystemServiceProvider::class,
        \components\Providers\TranslationServiceProvider::class,
        \Illuminate\Validation\ValidationServiceProvider::class,

        \Illuminate\Cache\CacheServiceProvider::class,

        //always last
        \components\Providers\AppServiceProvider::class,

    ],


    /*
    |--------------------------------------------------------------------------
    | Aliases
    |--------------------------------------------------------------------------
    */
    'aliases' => [
        'Route'   => \components\Facade\Route::class,
        'DB' => \Illuminate\Support\Facades\DB::class,
        'Validator' => \Illuminate\Support\Facades\Validator::class,
        'Cache' => \Illuminate\Support\Facades\Cache::class,
        'Event' => \Illuminate\Support\Facades\Event::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | DebugBar
    |--------------------------------------------------------------------------
    */
    'debugbar' => [
        'debug' => true,
    ]
];