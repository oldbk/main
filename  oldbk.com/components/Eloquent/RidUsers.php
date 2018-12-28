<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class RidUsers extends Model
{
    protected $table = 'rid_users';

    protected $primaryKey = 'owner';

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;


}