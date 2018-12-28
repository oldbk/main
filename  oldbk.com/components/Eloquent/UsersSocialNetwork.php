<?php

namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class UsersSocialNetwork extends Model
{
    protected $table = 'users_sn';

    protected $fillable = [
        'user_id',
        'sn_type',
        'sn_id',
    ];

    public $timestamps = false;


}