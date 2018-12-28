<?php

/**
 * Routing for encicl
 */
$path = '/encicl';
$app->group(
    $path,
    function () use ($app, $path) {
        $app->get(
            '/tools(/:action)',
            function ($action = 'index') use ($app) {
                new \components\Controller\ToolsController($app, $action);
            }
        )->via('GET', 'POST')->name('tools');

        $app->map(
            '/clans.html',
            function () use ($app) {
                new \components\Controller\encicl\EnciclController($app, 'clan');
            }
        )->via('GET', 'POST')->name('klans');

        $app->map(
            '/klani/clans.*',
            function () use ($app, $path) {
                $clanName = $app->request->get('clan', "");
                if (empty($clanName)) {
                    $link = '';
                } else {
                    $link = '?clan=' . $clanName;
                }
                $app->redirect($path . '/clans.html' . $link, 301);
            }
        )->via('GET', 'POST')->name('klans_2');

        $app->map(
            '(/(amulets|amun|armors|axe|boots|bow|clips|cloack|dubini|flower|helmet|kasteti|naruchi|rings|robi|shields|swords|mag1|predmeti|mag2|runs|eda|res)/:item).html',
            function ($item) use ($app) {
                new \components\Controller\encicl\ItemController($app, $item);
            }
        )->via('GET', 'POST')->name('encicl_item');

        $app->map(
            '(/(:page).html)?',
            function ($page = 'index') use ($app) {
                new \components\Controller\encicl\EnciclController($app, 'index', $page);
            }
        )->via('GET', 'POST')->name('encicl');

        $app->group(
            '/dressroom',
            function () use ($app) {
                $app->map(
                    '(/(:action)\.(html|json))?',
                    function ($action = 'index') use ($app) {
                        new \components\Controller\dressroom\DressroomController($app, $action);
                    }
                )->via('GET', 'POST')->name('encicl_dressroom');
            }
        );

        $app->map(
            '/',
            function () use ($app, $path) {
                $app->redirect($path, 301);
            }
        )->via('GET', 'POST')->name('encicl_redirect');
    }
);
