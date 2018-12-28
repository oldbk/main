<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class UsersPasCh extends Model
{
    protected $table = 'users_pas_ch';

    protected $primaryKey = 'owner';

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;

}