<?php

namespace components\Eloquent;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class News
 * @package components\Eloquent
 */
class News extends Model
{
    /**
     * @var string
     */
    protected $table = 'news';

    /***************************************************************************************************
     * Relationships
     **************************************************************************************************/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(News::class, 'parent', 'id');
    }

    /***************************************************************************************************
     * Scopes
     **************************************************************************************************/

    /**
     * @param $query
     * @return mixed
     */
    public function scopeHomeNews($query)
    {
        return $query->select([
                'id',
                'topic',
                'text',
                \DB::raw("STR_TO_DATE(`date`,'%d.%m.%y %H:%i') as cdate"),
            ])
            ->where('parent', 1)
            ->where(function($query)
            {
                $query->whereNull('print_time');
                $query->orWhere('print_time', '<' , Carbon::now()->timestamp);
            })
            ->orderBy('cdate', 'desc');
    }

    /***************************************************************************************************
     * Wrappers
     **************************************************************************************************/

}