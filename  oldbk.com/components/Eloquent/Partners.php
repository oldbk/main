<?php

namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Partners extends Model
{
    protected $table = 'partners';

    protected $guarded = [];

    public $timestamps = false;
}