<?php


namespace components\Eloquent;


use Illuminate\Database\Eloquent\Model;

class ForumModerator extends Model
{
    protected $table = 'forum_moderator';

    protected $guarded = [];

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    public $timestamps = false;

    /***************************************************************************************************
     * Relationships
     **************************************************************************************************/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    /***************************************************************************************************
     * Accessors & Mutators
     **************************************************************************************************/

    /**
     * @param $value
     * @return mixed
     */
    public function getPermissionsAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * @param $value
     */
    public function setPermissionsAttribute($value)
    {
        $this->attributes['permissions'] = $value ? serialize($value) : $value;
    }

}