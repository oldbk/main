<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Clan extends Model
{
    protected $table = 'clans';

    public $timestamps = false;


    /***************************************************************************************************
     * Relationships
     **************************************************************************************************/

    public function leader()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->hasMany(User::class, 'short', 'klan');
    }


}