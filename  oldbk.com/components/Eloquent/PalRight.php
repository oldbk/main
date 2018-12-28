<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class PalRight extends Model
{
    protected $table = 'pal_rights';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'pal_id';

    public $incrementing = false;


    /***************************************************************************************************
     * Accessors & Mutators
     **************************************************************************************************/

    /**
     * @param $value
     * @return mixed
     */
    public function getAbilsAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * @param $value
     */
    public function setAbilsAttribute($value)
    {
        $this->attributes['abils'] = serialize($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getForumPermissionAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * @param $value
     */
    public function setForumPermissionAttribute($value)
    {
        $this->attributes['forum_permission'] = $value ? serialize($value) : $value;
    }



    /***************************************************************************************************
     * Wrapper
     **************************************************************************************************/

    public function canTopMove()
    {
        return $this->top_move == 1;
    }




}