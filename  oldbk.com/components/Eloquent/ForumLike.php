<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;


class ForumLike extends Model
{
    protected $table = 'forum_like';

    protected $primaryKey = ['topic', 'user_id'];

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];

    /***************************************************************************************************
     * Relationships
     **************************************************************************************************/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(Forum::class, 'id', 'topic');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}