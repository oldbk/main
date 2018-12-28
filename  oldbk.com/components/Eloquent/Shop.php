<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'shop';

    protected $guarded = [];

    public $timestamps = false;
}