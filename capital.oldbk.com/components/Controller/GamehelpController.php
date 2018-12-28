<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 13.12.16
 * Time: 20:45
 */

namespace components\Controller;

use components\Controller\_base\BaseController;
use components\Component\Slim\Slim;
use components\models\Gamehelp;

class GamehelpController extends BaseController
{
	protected $page_id;

	public function __construct(Slim $container, $action = 'index' , $page = null)
	{
		$this->page_id = $page;

		parent::__construct($container, $action);
	}

	public function indexAction()
	{
		$page = Gamehelp::whereRaw('dir = ?', array($this->page_id))->first();

		if(!$page) {
			return $this->render('pagenotfound', array(

			));
		}

		return $this->render('index', array(
			'page' => $page->toArray(),
		));

		//$this->app->cache->set($this->getKeyCache(), $html, 600);
		//return $html;

	}
	/*
	protected function getKeyCache()
	{
		return sprintf('gamehelp_%s_%s_%s', $this->getControllerId(), $this->actionId, $this->page_id);
	}
	*/

}