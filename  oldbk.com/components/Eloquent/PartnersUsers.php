<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class PartnersUsers extends Model
{
    protected $table = 'partners_users';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;
}