<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class ConfirmPassword extends Model
{
    protected $table = 'confirmpasswd';

    protected $guarded = [];

    public $timestamps = false;


}