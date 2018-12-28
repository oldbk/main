<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 02.02.2016
 */

namespace components\Component\Quests;

use components\Component\AbstractComponent;
use components\Component\Config;
use components\models\User;

class QuestContainer extends AbstractComponent
{
    /** @var Quest[] */
    private $_quest = array();
	private $_user = null;

    protected function run()
    {

    }

	/**
	 * @param User|null $User
	 * @return Quest|QuestTest
	 */
    public function get($User = null)
	{
		if($User === null) {
			if($this->_user !== null) {
				$User = $this->_user;
			} else {
				$User = $this->app()->webUser->getUser();
			}
		}

		if(!isset($this->_quest[$User->id])) {
			$Quest = $this->getQuest();
			$this->_quest[$User->id] = $Quest->setUser($User)->get();
		}

		return $this->_quest[$User->id];
	}

	/**
	 * @param User $User
	 * @return $this
	 * @deprecated
	 */
	public function setUser($User)
	{
		$this->_user = $User;

		return $this;
	}

	private function getQuest()
	{
		if(Config::admins()) {
			return new QuestTest($this->app());
		}

		return new Quest($this->app());
	}

	public function __destruct()
	{
		unset($this->_quest);
		$this->_quest = array();
	}
}