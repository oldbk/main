<?php

namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class UsersIngame extends Model
{
    protected $table = 'users_ingame';

    protected $primaryKey = 'owner';

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;
}