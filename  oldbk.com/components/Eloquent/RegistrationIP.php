<?php

namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class RegistrationIP extends Model
{
    protected $table = 'reg_ip';

    protected $fillable = [
        'ip',
        'time',
    ];

    public $timestamps = false;

}