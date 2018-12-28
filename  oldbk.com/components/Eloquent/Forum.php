<?php


namespace components\Eloquent;


use components\Traits\ElasticaTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Forum
 * @package components\Eloquent
 */
class Forum extends Model
{
    use ElasticaTrait;

    protected $table = 'forum';

    public $timestamps = false;

    protected $guarded = [];

    /***************************************************************************************************
     * Accessors & Mutators
     **************************************************************************************************/


    /**
     * @param $value
     * @return mixed
     */
    public function getAInfoAttribute($value)
    {
        if (is_null($value)) return null;

        return explode(',', $value);
    }

    /**
     * @param $value
     */
    public function setAInfoAttribute($value)
    {
        $this->attributes['a_info'] = !is_null($value) ? join(',', $value) : null;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getCloseInfoAttribute($value)
    {
        if (is_null($value)) return null;

        return explode(',', $value);
    }

    /**
     * @param $value
     */
    public function setCloseInfoAttribute($value)
    {
        $this->attributes['close_info'] = !is_null($value) ? join(',', $value) : null;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getDelInfoAttribute($value)
    {
        if (is_null($value)) return null;

        return explode(',', $value);
    }

    /**
     * @param $value
     */
    public function setDelInfoAttribute($value)
    {
        $this->attributes['del_info'] = !is_null($value) ? join(',', $value) : null;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getDeltopInfoAttribute($value)
    {
        if (is_null($value)) return null;

        return explode(',', $value);
    }

    /**
     * @param $value
     */
    public function setDeltopInfoAttribute($value)
    {
        $this->attributes['deltop_info'] = !is_null($value) ? join(',', $value) : null;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getPalCommentsAttribute($value)
    {
        if (is_null($value)) return null;

        try {
            $pal_comments = unserialize($value);

            return $pal_comments;
        } catch (\Exception $exception) {
            $pal_comments = explode('|', $value);
        }

        $comments = null;
        foreach ($pal_comments as $comment) {
            $pl_inf = explode('_;_', $comment);

            if (!$pl_inf[0]) continue;

            $author = explode(',', $pl_inf[1]);
            array_push($author, $pl_inf[0]);

            $comments[] = [
                'author' => $author,
                'text' => $pl_inf[2],
            ];
        }

        return $comments;
    }

    /**
     * @param $value
     */
    public function setPalCommentsAttribute($value)
    {
        $this->attributes['pal_comments'] = !is_null($value) ? serialize($value) : null;
    }

    /***************************************************************************************************
     * Scopes
     **************************************************************************************************/



    /***************************************************************************************************
     * Relationships
     **************************************************************************************************/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(ForumLike::class, 'topic', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function above()
    {
        return $this->belongsTo(Forum::class, 'parent', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Forum::class, 'parent', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'author', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appeals()
    {
        return $this->hasMany(ForumAppeal::class, 'top_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function appeal()
    {
        return $this->hasOne(ForumAppeal::class, 'post_id', 'id');
    }


    /***************************************************************************************************
     * Wrappers
     **************************************************************************************************/

    /**
     * @param User|null $user
     * @return bool
     */
    public function hasAccess(User $user = null)
    {

        if ($this['min_align'] == 0 && $this['max_align'] == 0) {
            return true;
        }


        if ($user) {

            if (
                ($this->allowedAlignRange($user)) ||
                ($this['min_align'] == 9 && $this['max_align'] == 9 && $user->isDealer()) ||
                ($this['min_align'] == 11 && $this['max_align'] == 11 && $user->isHelper()) ||
                ($this['min_align'] == 10 && $this['max_align'] == 10 && $user['sex'] == 1) ||
                ($this['min_align'] == 1 && $this['max_align'] == 1 && $user->isHighPaladin()) ||
                ($this['min_align'] == 6 && $this['max_align'] == 6 && $user->isPaladin()) ||
                ($this['min_align'] == 12 && $this['max_align'] == 12 && $user->isDark() && $user->isLeaderClan()) || //главы тьмы
                ($this['min_align'] == 13 && $this['max_align'] == 13 && ($user->isLight() || $user->isHighPaladin()) && $user->isLeaderClan()) || // света
                ($this['min_align'] == 14 && $this['max_align'] == 14 && $user->isNeutral() && $user->isLeaderClan()) || // нейтралов
                ($this['min_align'] == 15 && $this['max_align'] == 15 && $user['in_tower'] != 4) || // доступ к сделкам, из темницы закрыт


                ($this['min_align'] == 6 && $this['max_align'] == 6 && $user->isAbsoluteChaos()) ||    //абсолютному хаосу дуступ к Светлой конфе
                ($this['min_align'] == 3 && $this['max_align'] == 3 && $user->isAbsoluteChaos()) ||    //абсолютному хаосу дуступ к ТЕмной
                ($this['min_align'] == 2 && $this['max_align'] == 2 && $user->isAbsoluteChaos()) ||    //абсолютному хаосу дуступ к Нейтралам

                ($user->isAdmin() || $user->isAdminion() || $user->isHighPaladin())) {

                return true;

            }

        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function allowedAlignRange(User $user)
    {
        return $user['align'] >= $this['min_align'] && $user['align'] <= $this['max_align'];
    }

    /**
     * @param User $user
     * @param string $type
     * @return bool
     */
    public function canSeeDeleted(User $user, $type = 'top')
    {
        $canSeeDeleted = 'canSeeDeleted' . ucfirst($type);
        return
            $user->isAdmin() ||
            $user->isAdminion() ||
            $user->isHighPaladin() ||
            (method_exists($user, $canSeeDeleted) && $user->$canSeeDeleted()) ||
            $this->getModeratorInfo($type)['id'] === $user->id;
    }

    /**
     * @return bool
     * Is fixed topic?
     */
    public function isFixed()
    {
        return $this->fix > 0;
    }

    /**
     * @return bool
     * Is closed topic?
     */
    public function isClosed()
    {
        return $this->close == 1;
    }

    /**
     * @return bool
     */
    public function hasClosedInfo()
    {
        return !is_null($this->close_info);
    }

    /**
     * @param $target
     * @return bool
     */
    public function isDeleted($target)
    {
        return $target == 'top' ? $this->deltoppal > 0 : $this->delpal > 0;
    }

    /**
     * @param $target
     * @return bool
     */
    public function hasDeletedInfo($target)
    {
        return $target == 'top' ? !is_null($this->deltoppal) : !is_null($this->delpal);
    }

    /**
     * @return bool
     */
    public function notMain()
    {
        if (!$this->above) {
            return true;
        }
        return $this->above->type !== 1;
    }

    /**
     * @return bool
     */
    public function isMain()
    {
        if (!$this->above) {
            return false;
        }
        return $this->above && $this->above->type === 1;
    }

    /**
     * @return bool
     */
    public function hasComments()
    {
        return $this->pal_comments ? true : false;
    }

    /**
     * @return array
     */
    public function getComments()
    {
        $pal_comments = $this->pal_comments;

        $comments = [];

        if ($pal_comments) {
            foreach ($pal_comments as $pal_comment) {

                $user = false;

                if ($pal_comment['author'] !== false) {
                    $user = [
                        'login' => $pal_comment['author'][0] ?? null,
                        'klan' => $pal_comment['author'][1] ?? null,
                        'align' => $pal_comment['author'][2] ?? null,
                        'level' => $pal_comment['author'][3] ?? '??',
                        'invisible' => (int)$pal_comment['author'][4] ?? 0,
                        'id' => $pal_comment['author'][5] ?? 0,
                    ];
                }

                $comments[] = [
                    'author' => $user,
                    'text' => $pal_comment['text'],
                ];

            }
        }

        return $comments;
    }

    /**
     * @param $target
     * @return array
     */
    public function getModeratorInfo($target)
    {
        switch ($target) {

            case 'top': {
                $info_target = $this->deltop_info;
                $info_id = $this->deltoppal;

                break;
            }

            case 'post': {
                $info_target = $this->del_info;
                $info_id = $this->delpal;

                break;
            }

            case 'close': {
                $info_target = $this->close_info ?: $this->deltop_info;
                $info_id = $this->closepal ?: $this->deltoppal;

                break;
            }

            default : {
                $info_target = $this->close_info ?: $this->deltop_info;
                $info_id = $this->closepal ?: $this->deltoppal;
            }
        }

        $transferred = (isset($info_target[5]) && $info_target[5] == 1);

        $user = [
            'login' => $info_target[0],
            'klan' => $info_target[1],
            'align' => $info_target[2],
            'level' => $info_target[3],
            'invisible' => $info_target[4],
            'transferred' => $transferred,
            'id' => $info_id,
        ];

        return $user;
    }


    /**
     * @param null $allowed_conf
     * @return bool
     */
    public function canCreateTopWithChaos($allowed_conf = null)
    {
        if (is_null($allowed_conf)) {
            $allowed_conf = [
                4, 12, 14, 17, 20, 21, 47,
            ];
        }

        if (is_array($allowed_conf)) {
            return in_array($this->id, $allowed_conf);
        }

        return $allowed_conf === $this->id;
    }

    /**
     * @param null $allowed_conf
     * @return bool
     */
    public function canCreatePostWithChaos($allowed_conf = null)
    {
        if (is_null($allowed_conf)) {
            $allowed_conf = [
                4, 12, 14, 17, 20, 21, 47,
            ];
        }

        if (is_array($allowed_conf)) {
            return in_array($this->above->id, $allowed_conf);
        }

        return $allowed_conf === $this->above->id;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canCreateWithSilens(User $user)
    {
        if(in_array($this->id, [47])) {
            return true;
        }

        return false;
    }



}