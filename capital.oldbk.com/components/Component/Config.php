<?php
namespace components\Component;
use components\Component\Slim\Slim;
use components\models\User;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */
class Config
{
    const ROOM_PHANTOM  = 431;
    const ROOM_ZNAHAR   = 43;

    //znahar
    public $znahar_free_max_level       = 3;
    public $znahar_min_ability          = 8;
    public $znahar_min_ability_view     = 7;
    public $znahar_min_magic          	= 8;
    public $znahar_min_magic_view       = 7;
    public $znahar_min_element          = 3;
    public $znahar_ability_drop_cost    = 1000;
    public $znahar_class_drop_cost    	= 50;

    //volna haosa
    public $volna_haos_start            = 0;
    public $volna_haos_end              = 0;

    //phantom
    public $phantom_enter_cost          = 5;
    public $phantom_enter_cost_type     = 1;

    //region klass ratio
	//показатели для танка
	public $klass_ratio_tank_uv 	= 0.125;
	public $klass_ratio_tank_krit 	= 0.125;
	//показатели для критовика
	public $klass_ratio_krit_uv 	= 0.25;
	//показатели для уворота
	public $klass_ratio_uv_krit 	= 0.25;
	//endregion

	public $ratio_damage_tank = 1;
	public $ratio_damage_krit = 1;
	public $ratio_damage_uvorot = 1;
	public $ratio_damage_unk = 1;

	public $street_speed = 4; //скорость передвижения по улици в секунда

    CONST LOGGING_DB    = 1;
    CONST LOGGING_ERROR = 1;

    private static $_instance = null;

    private function __construct(){}
    protected function __clone(){}
    public function import(){}
    public function get(){}

    public static function init()
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function isVolnaHaosa()
    {
        $time = new \DateTime();

        $volna_start = new \DateTime();
        $volna_start->setTimestamp($this->volna_haos_start);

        $volna_end = new \DateTime();
        $volna_end->setTimestamp($this->volna_haos_end);

        return $time >= $volna_start && $time <= $volna_end;
    }

    public static function canViewDebug($user_id = null, $ip = null)
    {
        $access_user_id = array(546433, 684792, 648, 182783, 689525, 102904, 8540, 14897, 7937, 697032, 698798, 698800, 698802, 698804, 698805, 698806, 698171);
        $access_ip_list = array('178.151.80.59');

        $keys = array('X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'REMOTE_ADDR');
        foreach ($keys as $key) {
            if (isset($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                foreach ($ips as $ip) {
                    if(in_array($ip, $access_ip_list)) {
                        return true;
                    }
                }
            } elseif($ip !== null && in_array($ip, $access_ip_list)) {
                return true;
            }
        }

        if(isset($_SESSION) && isset($_SESSION['uid']) && in_array($_SESSION['uid'], $access_user_id)) {
            return true;
        } elseif($user_id !== null && in_array($user_id, $access_user_id)) {
            return true;
        }

        return false;
    }

	/**
	 * @param User|array $User
	 * @return bool
	 */
    public static function isTester($User)
	{
		if(is_array($User)) {
			$User = new User($User);
		}

		if(self::canViewDebug($User->id) || $User->klan == 'pal' || $User->deal == -1) {
			return true;
		}

		return false;
	}

    public static function admins($user_id = null, $ip = null)
    {
        return self::canViewDebug($user_id, $ip);
    }
}