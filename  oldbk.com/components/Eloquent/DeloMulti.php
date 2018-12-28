<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class DeloMulti extends Model
{
    protected $table = 'delo_multi';

    protected $guarded = [];

    public $timestamps = false;
}