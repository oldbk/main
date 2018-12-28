<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $guarded = [];

    public $timestamps = false;


}