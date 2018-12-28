<?php
namespace components\Component;

use Illuminate\Contracts\Container\Container;
use Illuminate\Config\Repository as ConfigRepository;

/**
 * Class Config
 * @package components\Component
 */
class Config extends ConfigRepository
{
    const ROOM_PHANTOM  = 431;
    const ROOM_ZNAHAR   = 43;

    //znahar
    public $znahar_free_max_level       = 3;
    public $znahar_min_ability          = 3;
    public $znahar_min_element          = 3;
    public $znahar_ability_drop_cost    = 1000;

    //volna haosa
    public $volna_haos_start            = 0;
    public $volna_haos_end              = 0;

    //phantom
    public $phantom_enter_cost          = 5;
    public $phantom_enter_cost_type     = 1;

    const LOGGING_DB    = 0;
    const LOGGING_ERROR = 1;

    private static $_instance = null;

    protected $app;

    public function __construct(Container $app = null)
    {
        $settings = [];

        if (!is_null($app)) {
            $this->app = $app;
            $settings = $app->getContainer()->settings;
        }

        parent::__construct($settings);
    }

    /**
     * @return Config|null
     */
    public static function init()
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return bool
     */
    public function isVolnaHaosa()
    {
        $time = new \DateTime();

        $volna_start = new \DateTime();
        $volna_start->setTimestamp($this->volna_haos_start);

        $volna_end = new \DateTime();
        $volna_end->setTimestamp($this->volna_haos_end);

        return $time >= $volna_start && $time <= $volna_end;
    }

    /**
     * @param null $user_id
     * @param null $ip
     * @return bool
     */
    public static function canViewDebug($user_id = null, $ip = null)
    {
        $access_user_id = array(546433, 684792, 648, 182783, 689525, 102904, 8540, 14897);
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
     * @param null $user_id
     * @param null $ip
     * @return bool
     */
    public static function admins($user_id = null, $ip = null)
    {
        return self::canViewDebug($user_id, $ip);
    }
}