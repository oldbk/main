<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Invites extends Model
{
    protected $table = 'invites';

    protected $guarded = [];

    public $timestamps = false;


}