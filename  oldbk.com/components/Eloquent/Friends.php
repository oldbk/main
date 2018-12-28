<?php

namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Friends extends Model
{
    protected $table = 'friends';

    protected $guarded = [];

    public $timestamps = false;
}