<?php
namespace components\Controller;
use components\Component\VarDumper;
use \components\Controller\_base\MainController;
use components\models\CraftProf;
use components\models\User;
use components\models\UsersCraft;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 *
 */
class EffectsController extends MainController
{
	public function craftAction()
	{
		$config = $this->app->container->get('config.craft');

		$profs = CraftProf::whereRaw('type = 0 and name != "noprof"')->get()->toArray();
		$UserProfs = UsersCraft::firstOrCreate(['owner' => $this->app->webUser->getId()]);


		$this->render('craft', ['profs' => $profs, 'UserProfs' => $UserProfs, 'config' => $config, 'menu' => 1]);
	}

	public function questAction()
	{
		$unik_config = $this->app->container->get('config.unik');

		/** @var User $User */
		$User = $this->app->webUser->getUser();
		$Quest = $this->app->quest->get();
		$bonuses = $User->getUandUU();
		$bonuses['u'] += $bonuses['uu'];
		$uBonus = User::getTypeU($bonuses['u']);
		$uuBonus = User::getTypeUU($bonuses['uu']);

		$uWeight = isset($unik_config['u'][$uBonus]) ? $unik_config['u'][$uBonus]['weight'] : 0;
		$uuWeight = isset($unik_config['uu'][$uuBonus]) ? $unik_config['uu'][$uuBonus]['weight'] : 0;

		$bonus = 0;
		if($bonuses['u']) {
			$bonus = isset($unik_config['u'][$uBonus+1]) ? $unik_config['u'][$uBonus+1] : $unik_config['u'][$uBonus];
			$bonus['count'] = $bonuses['u'];
		}

		if($uuWeight > $uWeight || ($uuWeight == $uWeight && $bonuses['uu'] > $bonuses['u']) || $uWeight == 4) {
			$bonus = isset($unik_config['uu'][$uuBonus+1]) ? $unik_config['uu'][$uuBonus+1] : $unik_config['uu'][$uuBonus];
			$bonus['count'] = $bonuses['uu'];
		}

		$this->render('quest', ['items' => $Quest->getDescriptions(), 'bonus' => $bonus, 'menu'=> 5]);
	}
}