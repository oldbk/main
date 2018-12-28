<?php

namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class IpLog extends Model
{
    protected $table = 'iplog';

    protected $guarded = [];

    public $timestamps = false;
}