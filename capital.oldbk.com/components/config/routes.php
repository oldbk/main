<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 22.08.17
 * Time: 20:07
 *
 * @var \components\Component\Slim\Slim $app
 */

$app->group('/help', function () use ($app) {
	$app->map('/(:page).html', function ($page) use ($app) {
		new \components\Controller\GamehelpController($app, 'index', $page);
	})->via('GET', 'POST')->name('gamehelp');
});

$app->group('/action', function () use ($app) {
	$app->group('/oauth', function () use ($app) {
		$app->get('/facebook/:action', function ($action) use ($app) {
			new \components\Controller\oauth\FacebookController($app, $action);
		})->name('oauth.facebook');

		$app->get('/google/:action', function ($action) use ($app) {
			new \components\Controller\oauth\GoogleController($app, $action);
		})->name('oauth.google');

		$app->get('/instagram/:action', function ($action) use ($app) {
			new \components\Controller\oauth\InstagramController($app, $action);
		})->name('oauth.instagram');

		$app->get('/mailru/:action', function ($action) use ($app) {
			new \components\Controller\oauth\MailruController($app, $action);
		})->name('oauth.mailru');

		$app->get('/ok/:action', function ($action) use ($app) {
			new \components\Controller\oauth\OkController($app, $action);
		})->name('oauth.ok');

		$app->get('/twitter/:action', function ($action) use ($app) {
			new \components\Controller\oauth\TwitterController($app, $action);
		})->name('oauth.twitter');

		$app->get('/vkontakte/:action', function ($action) use ($app) {
			new \components\Controller\oauth\VkontakteController($app, $action);
		})->name('oauth.vkontakte');
	});

	$app->group('/street', function () use ($app) {
		$app->get('/clan(/:action)', function ($action = 'index') use ($app) {
			new \components\Controller\street\ClanStreetController($app, $action);
		})->name('street.clan');
	});

	$app->map('/api/:action', function ($action) use ($app) {
		new \components\Controller\ApiController($app, $action);
	})->via('GET', 'POST')->name('api');

	$app->map('/znahar/:action', function ($action) use ($app) {
		new \components\Controller\ZnaharController($app, $action);
	})->via('GET', 'POST')->name('znahar');

	$app->map('/security/:action', function ($action) use ($app) {
		new \components\Controller\SecurityController($app, $action);
	})->via('GET', 'POST')->name('security');

	$app->map('/quest/:action', function ($action) use ($app) {
		new \components\Controller\QuestController($app, $action);
	})->via('GET', 'POST')->name('quest');

	$app->map('/dialog/:action', function ($action = 'dialog') use ($app) {
		new \components\Controller\DialogController($app, $action);
	})->via('GET', 'POST')->name('dialog');

	$app->map('/tools/:action', function ($action) use ($app) {
		new \components\Controller\ToolsController($app, $action);
	})->via('GET', 'POST')->name('tools');

	$app->map('/fix/:action', function ($action) use ($app) {
		new \components\Controller\FixesController($app, $action);
	})->via('GET', 'POST')->name('fix');

	$app->map('/bank/:action', function ($action) use ($app) {
		new \components\Controller\BankController($app, $action);
	})->via('GET', 'POST')->name('bank');

	$app->map('/reward/:action', function ($action) use ($app) {
		new \components\Controller\RewardController($app, $action);
	})->via('GET', 'POST')->name('reward');

	$app->map('/effects(/:action)', function ($action = 'craft') use ($app) {
		new \components\Controller\EffectsController($app, $action);
	})->via('GET', 'POST')->name('effects');

	$app->map('/wc(/:action)', function ($action = 'index') use ($app) {
		new \components\Controller\WcController($app, $action);
	})->via('GET', 'POST')->name('wc');

	$app->map('/rating(/:action)', function ($action = 'index') use ($app) {
		new \components\Controller\RatingController($app, $action);
	})->via('GET', 'POST')->name('rating');

	$app->map('/fontan/:action', function ($action) use ($app) {
		new \components\Controller\FontanController($app, $action);
	})->via('GET', 'POST')->name('fontan');

	$app->map('/tournament(/:action)', function ($action = 'index') use ($app) {
		new \components\Controller\TournamentController($app, $action);
	})->via('GET', 'POST')->name('clan.tournament');

	$app->map('/map-editor/:action', function ($action) use ($app) {
		new \components\Controller\MapEditorController($app, $action);
	})->via('GET', 'POST')->name('map_editor');
});