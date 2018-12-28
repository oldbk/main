<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class UsersNickHist extends Model
{
    protected $table = 'users_nick_hist';

    public $timestamps = false;

    protected $guarded = [];

}