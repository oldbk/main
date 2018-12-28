<?php

namespace components\Eloquent;


use Carbon\Carbon;
use components\Enum\EffectType;
use components\Enum\Forum\ForumPermission;
use components\Enum\PermissionInterface;
use components\Enum\User\AlignPermission;
use components\Enum\UserAlign;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package components\Eloquent
 *
 * @property string $salt
 */
class User extends Model
{
    protected $table = 'users';

    protected $guarded = ['id'];

    /**
     * @var bool
     */
    public $timestamps = false;


    /***************************************************************************************************
     * Accessors & Mutators
     **************************************************************************************************/

    /**
     * @param $value
     * @return mixed
     */
    public function getGruppovuhaAttribute($value)
    {
        if (is_null($value)) return null;

        try {

            $gr = unserialize($value);

        } catch (\Exception $exception) {
            $gr = array_fill(0, 9, 1);
            $this->update([
                'gruppovuha' => $gr
            ]);
        }

        return $gr;
    }

    /**
     * @param $value
     */
    public function setGruppovuhaAttribute($value)
    {
        $this->attributes['gruppovuha'] = !is_null($value) ? serialize($value) : null;
    }


    /***************************************************************************************************
     * Relationships
     **************************************************************************************************/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clan()
    {
        return $this->belongsTo(Clan::class, 'klan', 'short');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function effects()
    {
        return $this->hasMany(Effects::class, 'owner', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rights()
    {
        return $this->hasOne(PalRight::class, 'pal_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Forum::class, 'author', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(ForumLike::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function moderator()
    {
        return $this->hasOne(ForumModerator::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moderatorAppeals()
    {
        return $this->hasMany(ForumAppeal::class, 'moderator_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loginHistory()
    {
        return $this->hasMany(UsersNickHist::class, 'uid', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user2fa()
    {
        return $this->hasOne(User2fa::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partnersUsers()
    {
        return $this->hasOne(PartnersUsers::class, 'id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function usersInGame()
    {
        return $this->hasOne(UsersIngame::class, 'owner', 'id');
    }


    /***************************************************************************************************
     * Scopes
     **************************************************************************************************/

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotAdmin($query)
    {
        return $query
            ->where('klan', '!=', 'radminion')
            ->where('klan', '!=', 'Adminion');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotBlocked($query)
    {
        return $query->where('block', '=', 0);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotBot($query)
    {
        return $query->where('bot', '=', 0);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRateWins($query)
    {
        return $query->select([
            'id',
            'login',
            'level',
            'klan',
            'align',
            'win',
        ])
            ->notAdmin()
            ->notBlocked()
            ->notBot()
            ->orderBy('win', 'desc')
            ->limit(10);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRateVoins($query)
    {
        return $query->select([
            'id',
            'login',
            'level',
            'klan',
            'align',
            'voinst',
        ])
            ->notAdmin()
            ->notBlocked()
            ->notBot()
            ->orderBy('voinst', 'desc')
            ->limit(10);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGrandBattles($query)
    {
        return $query->select([
            'id',
            'login',
            'level',
            'klan',
            'align',
            'winstbat',
        ])
            ->notAdmin()
            ->notBlocked()
            ->notBot()
            ->orderBy('winstbat', 'desc')
            ->limit(10);
    }

    /**
     * @param $query
     * @param $level
     * @return mixed
     */
    public function scopeRateSkulls($query, $level)
    {
        return $query->select([
            'id',
            'login',
            'level',
            'klan',
            'align',
            'skulls',
        ])
            ->notAdmin()
            ->notBlocked()
            ->notBot()
            ->where('level', $level)
            ->where('align', '!=', 4)
            ->where('skulls', '>', 0)
            ->orderBy('skulls', 'desc')
            ->limit(10);
    }

    /**
     * @param $query
     * @param $login
     * @return mixed
     */
    public function scopeWhereLogin($query, $login)
    {
        return $query->where('login', '=', $login);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePaladins($query)
    {
        return $query->where('align', '>', 1)->where('align', '<', 2);
    }


    /***************************************************************************************************
     * Wrapper
     **************************************************************************************************/

    public function isHighPaladin()
    {
        return $this->align == 1.99;
    }

    /**
     * @return bool
     */
    public function isPaladin()
    {
        return $this->align > 1 && $this->align < 2;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return filled($this->klan) && ($this->klan == 'Adminion' || $this->klan == 'radminion');
    }

    /**
     * @return bool
     */
    public function isAdminion()
    {
        return $this->align > 2 && $this->align < 3;
    }

    /**
     * @return bool
     */
    public function isRAdminion()
    {
        return $this->align == 2.4;
    }

    /**
     * @return bool
     */
    public function isSimpleUser()
    {
        return !$this->isAdminion() && !$this->isAdmin();
    }

    /**
     * @return bool
     */
    public function isNeutral()
    {
        return $this->align == UserAlign::NEUTRAL;
    }

    /**
     * @return bool
     */
    public function isGray()
    {
        return $this->align == UserAlign::GRAY;
    }

    /**
     * @return bool
     */
    public function isDark()
    {
        return $this->align == UserAlign::DARK;
    }

    /**
     * @return bool
     */
    public function isLight()
    {
        return $this->align == UserAlign::LIGHT;
    }

    /**
     * @return bool
     */
    public function isDealer()
    {
        return $this->deal === 1;
    }

    /**
     * @return bool
     */
    public function isHelper()
    {
        return $this->deal === -1;
    }

	/**
	 * @return bool
	 */
    public function isTester()
	{
		if($this->isHelper() || $this->isPaladin() || $this->isAdmin()) {
			return true;
		}

		return false;
	}

    /**
     * @return bool
     */
    public function isLeaderClan()
    {
        return
            $this->klan &&
            $this->clan &&
            $this->clan->glava == $this->id &&
            $this->clan->time_to_del == 0;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->getAttribute('hidden') > 0;
    }

    /**
     * @return mixed
     */
    public function getHiddenEffect()
    {
        return $this->getEffect(EffectType::INVISIBLE);
    }

    /**
     * @return bool
     */
    public function isBlocked()
    {
        return $this->block > 0;
    }

    /**
     * @return bool
     */
    public function hasSecondPassword()
    {
        return filled($this->second_password);
    }

    /**
     * @return bool
     */
    public function hasChaos()
    {
        return $this->align == UserAlign::CHAOS;
    }

    /**
     * @return mixed
     */
    public function getChaos()
    {
        return $this->getEffect(EffectType::CHAOS);
    }

    /**
     * @return bool
     */
    public function isAbsoluteChaos()
    {
        return $this->align == UserAlign::ACHAOS;
    }

    /**
     * @return bool
     */
    public function hasForumSilence()
    {
        return $this->hasEffect(EffectType::SLEEPF);
    }

    /**
     * @return mixed
     */
    public function getForumSilence()
    {
        return $this->getEffect(EffectType::SLEEPF);
    }

    /**
     * @param $type
     * @return bool
     */
    public function hasEffect($type)
    {
        return $this->effects()
                ->where('type', $type)
                ->where(function ($query) {
                    $query->whereNull('time');
                    $query->orWhere('time', '>', Carbon::now()->timestamp);
                })
                ->count() > 0;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getEffect($type)
    {
        return $this->effects()
            ->where('type', $type)
            ->where(function ($query) {
                $query->whereNull('time');
                $query->orWhere('time', '>', Carbon::now()->timestamp);
            })
            ->first();
    }

    /**
     * @param array $types
     * @return bool
     */
    public function hasAnyEffect(array $types)
    {
        return $this->effects()->whereIn('type', $types)
                ->where(function ($query) {
                    $query->whereNull('time');
                    $query->orWhere('time', '>', Carbon::now()->timestamp);
                })
                ->count() > 0;
    }

    /**
     * @param array $types
     * @return \Illuminate\Support\Collection
     */
    public function getEffects($types = [])
    {
        $types = (array)$types;

        $effects = collect();

        $effDataQuery = $this->effects();

        if (!empty($types)) {
            $effDataQuery->whereIn('type', $types);
        }

        $effData = $effDataQuery->where(function ($query) {
                    $query->whereNull('time');
                    $query->orWhere('time', '>', Carbon::now()->timestamp);
                })
                ->get();

        foreach ($effData as $effect) {

            if ($effect->isFinished()) continue;

            $effects->push($effect);

        }

        return $effects;
    }


    /**
     * @param PermissionInterface $permission
     * @return bool
     */
    public function hasPermission(PermissionInterface $permission)
    {

        if ($this->isAdmin() || $this->isAdminion()) {
            return true;
        }


        if ($this->hasModeratorPermission($permission)) {
            return true;
        }

        //права по айдишнику
        $permissions_id = $permission::getPermissionsForId()[$this->id] ?? false;


        if (!is_array($permissions_id))
            return false;

        if (in_array($permission->getValue(), $permissions_id)) {
            return true;
        }

        return false;
    }

    /**
     * @param PermissionInterface $permission
     * @return bool
     */
    public function hasModeratorPermission(PermissionInterface $permission)
    {

        if (!$this->moderator) {
            return false;
        }

        $const = $permission::getConstants();

        $key = array_search($permission->getValue(), $const);

        if (!$key) {
            return false;
        }

        if (is_array($this->moderator->permissions) && in_array(strtolower($key), $this->moderator->permissions)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isForumModerator()
    {
        return $this->hasPermission(ForumPermission::FORUM_MODERATOR());
    }

    /**
     * @return bool
     */
    public function canTopMove()
    {
        return $this->hasPermission(ForumPermission::CAN_TOP_MOVE());
    }

    /**
     * @return bool
     */
    public function canTopFix()
    {
        return $this->hasPermission(ForumPermission::CAN_TOP_FIX());
    }

    /**
     * @return bool
     */
    public function canTopUnFix()
    {
        return $this->hasPermission(ForumPermission::CAN_TOP_UNFIX());
    }

    /**
     * @return bool
     */
    public function canTopClose()
    {
        return $this->hasPermission(ForumPermission::CAN_TOP_CLOSE());
    }

    /**
     * @return bool
     */
    public function canTopOpen()
    {
        return $this->hasPermission(ForumPermission::CAN_TOP_OPEN());
    }

    /**
     * @param bool $hard
     * @return bool
     */
    public function canTopDelete($hard = false)
    {
        if ($hard !== false) {
            return $this->hasPermission(ForumPermission::CAN_TOP_DELETE_HARD());
        }
        return $this->hasPermission(ForumPermission::CAN_TOP_DELETE());
    }

    /**
     * @return bool
     */
    public function canTopRestore()
    {
        return $this->hasPermission(ForumPermission::CAN_TOP_RESTORE());
    }

    /**
     * @return bool
     */
    public function canTopDeletePosts()
    {
        return $this->hasPermission(ForumPermission::CAN_TOP_DELETE_POSTS());
    }

    /**
     * @return bool
     */
    public function canCommentWrite()
    {
        return /*$this->rights->red_forum == 1 || */
            $this->hasPermission(ForumPermission::CAN_COMMENT_WRITE());
    }

    /**
     * @return bool
     */
    public function canCommentDelete()
    {
        return $this->hasPermission(ForumPermission::CAN_COMMENT_DELETE());
    }

    /**
     * @param bool $hard
     * @return bool
     */
    public function canPostDelete($hard = false)
    {
        if ($hard !== false) {
            return $this->hasPermission(ForumPermission::CAN_POST_DELETE_HARD());
        }
        return $this->hasPermission(ForumPermission::CAN_POST_DELETE());
    }

    /**
     * @return bool
     */
    public function canPostRestore()
    {
        return $this->hasPermission(ForumPermission::CAN_POST_RESTORE());
    }

    /**
     * @return bool
     */
    public function canSeeDeletedPost()
    {
        return $this->hasPermission(ForumPermission::CAN_SEE_DELETED_POST());
    }

    /**
     * @return bool
     */
    public function canSeeDeletedTop()
    {
        return $this->hasPermission(ForumPermission::CAN_SEE_DELETED_TOP());
    }

    /**
     * @return bool
     */
    public function canEditPost()
    {
        return $this->hasPermission(ForumPermission::CAN_EDIT_POST());
    }

    /**
     * @return bool
     */
    public function canSeeWhoModerator()
    {
        return $this->hasPermission(ForumPermission::CAN_SEE_WHO_MODERATOR());
    }

    /**
     * @return bool
     */
    public function canSeeInvisibleAuthor()
    {
        return $this->hasPermission(ForumPermission::CAN_SEE_INVISIBLE_AUTHOR());
    }

    /**
     * @return bool
     */
    public function canManageAppeals()
    {
        return $this->hasPermission(ForumPermission::CAN_MANAGE_APPEAL());
    }

    /**
     * @return bool
     */
    public function canManageCategories()
    {
        return $this->isAdmin() || $this->isAdminion();
    }

    /**
     * @return bool
     */
    public function canManagePermissions()
    {
        return
            $this->isAdmin() ||
            $this->isAdminion() ||
            $this->isHighPaladin() ||
            $this->hasPermission(AlignPermission::CAN_MANAGE_PERMISSION());
    }

    /**
     * @return bool
     */
    public function canBeInvisible()
    {
        return $this->hasPermission(ForumPermission::CAN_BE_INVISIBLE());
    }

    /**
     * @return string
     */
    public static function generateSalt()
    {
        return md5(md5(time()). time());
    }

    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return $this->pass == self::generatePassword($password, $this->salt);
    }

    /**
     * @param $login
     * @param $password
     * @param $salt
     * @return string
     */
    public static function generatePassword($password, $salt)
    {
        return md5(md5($password) . $salt);
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        if ($this->isHighPaladin()) {
            return 'Верховный Паладин';
        } elseif ($this->isPaladin()) {
            return 'Паладин';
        } elseif ($this->isAdmin() || $this->isAdminion()) {
            return 'Ангел';
        } elseif ($this->isDealer()) {
            return 'Дилер';
        } elseif ($this->isHelper()) {
            return 'Помощник';
        } else {
            return 'Персонаж';
        }
    }
}
