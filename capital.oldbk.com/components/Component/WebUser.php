<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 02.02.2016
 */

namespace components\Component;


use components\models\PalRights;
use components\models\User;

class WebUser extends AbstractComponent
{
    /** @var User */
    private $_user;
    private $_is_guest = true;

    protected function run()
    {
        try {
            if(!$this->app()->session->get('uid')) {
                $this->_user = null;
                $this->_is_guest = true;
                throw new \Exception();
            } else {
                $this->login();
            }

        } catch (\Exception $ex) {

        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function login()
    {
        $user_id = (int)$this->app()->session->get('uid');
        if($user_id > 0) {
            $this->_user = User::find($user_id);
            if($this->_user) {
                $this->_is_guest = false;
                return true;
            }
        }

        return false;
    }

    public function getUser()
    {
        if(!$this->_user && !$this->login()) {
            return null;
        }

        return $this->_user;
    }

    public function getId()
    {
        $user = $this->getUser();
        if(!$user) {
            return null;
        }

        return $user->id;
    }

    public function getLogin()
    {
        $user = $this->getUser();
        if(!$user) {
            return null;
        }

        return $user->login;
    }

    public function getAlign()
    {
        $user = $this->getUser();
        if(!$user) {
            return null;
        }

        return $user->align;
    }

    public function getLevel()
    {
        $user = $this->getUser();
        if(!$user) {
            return null;
        }

        return $user->level;
    }

    public function getRoom()
    {
        $user = $this->getUser();
        if(!$user) {
            return null;
        }

        return $user->room;
    }

    public function getKlan()
    {
        $user = $this->getUser();
        if(!$user) {
            return null;
        }

        return $user->klan;
    }

    public function isGuest()
    {
        return $this->_is_guest;
    }

    public function getCityId()
    {
        $user = $this->getUser();
        if(!$user) {
            return null;
        }

        return $user->id_city;
    }

    public function getGender()
    {
        $user = $this->getUser();
        if(!$user) {
            return null;
        }

        return $user->sex;
    }

    public function isAdmin()
    {
        return in_array($this->getId(), array(546433)) || Config::admins($this->getId());
    }

    private $_access = [
    	'i_angel' 				=> 0,
    	'i_pal' 				=> 0,

    	'can_forum_del' 		=> 0,
    	'can_forum_restore' 	=> 0,
    	'can_close_top' 		=> 0,
    	'can_open_top' 			=> 0,
    	'can_del_top' 			=> 0,
    	'can_del_top_all' 		=> 0,
    	'can_rest_top_all' 		=> 0,
    	'can_del_pal_comments' 	=> 0,
    	'can_create_votes' 		=> 0,

		'view_ekr' 				=> 0,
		'can_comment' 			=> 0,
		'can_top_move' 			=> 0,
		'perevodi' 				=> 0,
		'item_hist' 			=> 0,
		'pal_tel' 				=> 0,
		'zhhistory' 			=> 0,

		'klans_kazna_view' 		=> 0,
		'klans_kazna_logs' 		=> 0,
		'klans_ars_logs' 		=> 0,

		'klans_ars_put' 		=> 0,

		'pals_delo' 			=> 0,
		'pals_online' 			=> 0,

		'anonim_hist' 			=> 0,
		'abils' 				=> 0,
		'loginip' 				=> 0,
		'viewmanyips' 			=> 0,
	];

    /** @var PalRights */
    private $_PalRights = null;
    public function checkAccess($access)
	{
		if(!$this->_PalRights) {
			$this->_PalRights = PalRights::where('pal_id', '=', $this->getId())->first();
		}

		if ($this->getAlign() > 2 && $this->getAlign() < 3) {
			$this->_access['i_angel'] = $this->getAlign();
		}

		if(
			($this->getAlign() > 1 && $this->getAlign() < 2) ||
			($this->_access['i_angel'] > 0) ||
			($this->getAlign() == 7) ||
			($this->getAlign() == 5) ||
			($this->getId() == 697032) ||
			($this->getId() == 5)
		) {
			$this->_access['i_pal'] = $this->getAlign();
			$this->_access['can_forum_del'] = (($this->getAlign() >= 1.5 && $this->getAlign() < 2) || $this->getAlign() == 7 || $this->_access['i_angel'] > 0) ? 1 : 0;
			$this->_access['can_forum_restore'] = (($this->getAlign() >= 1.91 && $this->getAlign() < 2) || $this->getAlign() == 7 || $this->_access['i_angel'] > 0) ? 1 : 0;
		}

		if(isset($this->_access[$access]) && $this->_access[$access] > 0) {
			return true;
		}

		return false;
	}
}