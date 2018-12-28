<?php

namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class UsersCounter extends Model
{
    protected $table = 'users_counter';

    protected $primaryKey = 'owner';

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;
}