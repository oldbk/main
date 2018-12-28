<?php

/**
 * News
 */
$app->group('/news', function () use ($app) {
    $app->map('/:id', function ($id) use ($app) {
        new \components\Controller\news\NewsController($app, 'post', $id);
    })->via('GET', 'POST')->name('news_post_id')->conditions(['id' => '\d+']);


    $app->map('/delete_comment/:id', function ($id) use ($app) {
        new \components\Controller\news\NewsController($app, 'deleteComment', $id);
    })->via('GET', 'POST')->name('news_post_delete_comment')->conditions(['id' => '\d+']);


	$app->map('', function () use ($app) {
		new \components\Controller\news\NewsController($app, 'news');
	})->via('GET', 'POST')->name('news');


	$app->map('/', function () use ($app) {
			$app->redirect('/news', 301);
	})->via('GET', 'POST')->name('news_redirect');
});