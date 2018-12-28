<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Telegraph extends Model
{
    protected $table = 'telegraph';

    protected $guarded = [];

    public $timestamps = false;


}