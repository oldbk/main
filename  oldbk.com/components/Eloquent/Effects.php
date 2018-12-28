<?php


namespace components\Eloquent;


use Carbon\Carbon;
use components\Enum\EffectType;
use Illuminate\Database\Eloquent\Model;

class Effects extends Model
{
    protected $table = 'effects';

    public $timestamps = false;


    /***************************************************************************************************
     * Wrapper
     **************************************************************************************************/

    /**
     * @return bool
     */
    public function isFinished()
    {
        return !is_null($this->time) && Carbon::now()->greaterThanOrEqualTo(Carbon::createFromTimestamp($this->time));
    }

    /**
     * @return bool
     */
    public function isInvisible()
    {
        return $this->type == EffectType::INVISIBLE;
    }

    /**
     * @return bool
     */
    public function isSleep()
    {
        return $this->type == EffectType::SLEEP;
    }

    /**
     * @return bool
     */
    public function isSleepf()
    {
        return $this->type == EffectType::SLEEPF;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return \Lang::get('user.condition.' . $this->type . '.description');
    }

}