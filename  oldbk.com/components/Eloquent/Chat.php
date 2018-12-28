<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chat';

    public $timestamps = false;

    protected $guarded = [];

}