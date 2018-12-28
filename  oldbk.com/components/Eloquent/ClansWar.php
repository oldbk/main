<?php

namespace components\Eloquent;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ClansWar extends Model
{
    protected $table = 'clans_war_new';

    public function scopeCurrentWars($query)
    {
        return
            $query->select([
                '*',
            ])
            ->where('winner', 0)
            ->where('stime', '<=', Carbon::now())
            ->orderBy('id', 'desc');
    }

}