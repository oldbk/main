<?php
namespace components\Controller;


use Carbon\Carbon;
use components\Controller\_base\BaseController;
use components\Component\Slim\Middleware\ClientScript\ClientScript;
use components\Eloquent\Bank;
use components\Eloquent\ClansWar;
use components\Eloquent\ConfirmPassword;
use components\Eloquent\Ivents;
use components\Eloquent\Lichka;
use components\Eloquent\News;
use components\Eloquent\Partners;
use components\Eloquent\User;
use components\Enum\Season;
use components\Exceptions\ReminderPasswordException;

/**
 * Class HomeController
 * @package components\Controller
 */
class CacheController extends BaseController
{

    /**
     * Главная
     * @return string
     */
    public function newsAction()
    {
		if($this->app->cache->isExisting('home_news')) {
			$this->app->cache->delete('home_news');
		}

		$this->renderJSON([
			'status' => 1,
			'message' => 'clear cache ok'
		]);
    }

	public function koAction()
	{
		if(!$this->app->cache->isExisting('configKo')) {
			$this->renderJSON(array(
				'status' => 1,
				'message' => 'Cache not found for configKo',
			));
		}

		$this->app->cache->delete('configKo');
		$this->renderJSON(array(
			'status' => 1,
			'message' => 'Cache remove for config ko ',
		));
	}

	public function allAction()
	{
		$this->app->cache->clean();

		$this->renderJSON(array(
			'ok' => true
		));
	}
}