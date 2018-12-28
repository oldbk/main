<?php

namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class UserAdvert extends Model
{
    protected $table = 'user_advert';

    protected $guarded = [];

    public $timestamps = false;
}